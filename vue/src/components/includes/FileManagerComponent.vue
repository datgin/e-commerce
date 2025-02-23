<template>
	<div
		class="modal fade show d-flex align-items-center justify-content-center h-100 w-100"
		@click.self="closeModal"
		v-if="visible"
	>
		<div
			class="container-fluid position-absolute mx-auto shadow"
			:style="{ top: top + 'px', left: left + 'px', transform: 'translate(-50%, -50%)' }"
			@mousedown="startDrag"
		>
			<div
				class="header d-flex cursor-move justify-content-end border-bottom bg-light py-3 pe-3 cursor-move"
				@mousedown.stop="enableDrag"
			>
				<button class="btn btn-link p-0" type="button" @click="closeModal">
					<i class="bi bi-x-square-fill fs-5"></i>
				</button>
			</div>
			<div class="mt-3">
				<div class="d-flex justify-content-between border-bottom px-2 pb-3">
					<div class="btn-group">
						<input
							type="file"
							class="d-none"
							multiple
							ref="fileInputRef"
							@change="handleUploadFile"
						/>
						<button class="btn btn-primary rounded-start" @click="fileInputRef?.click()">
							<i class="bi bi-cloud-arrow-up-fill me-2"></i>
							<span>Tải tệp lên</span>
							<span v-if="selectedFileInput" class="ms-2 fw-bold text-primary">
								({{ selectedFileInput.length }}) tệp.
							</span>
						</button>
						<button class="btn btn-secondary" @click="fetchData">
							<i class="bi bi-arrow-clockwise me-2"></i>
							<span>Tải lại trang</span>
						</button>
					</div>
				</div>
				<div class="row">
					<div class="col-9 overflow-auto" style="max-height: 65vh; overflow-y: auto">
						<div class="spinner-overlay" v-if="loading">
							<div class="spinner-border" role="status"></div>
						</div>
						<div class="row g-3 px-2" :class="{ blur: loading }">
							<div class="row g-3">
								<div
									class="card-image col-6 col-md-4 col-lg-2"
									v-for="image in dataImage"
									:key="image.id"
									@click="chooseFile(image)"
									:class="selectedFile.includes(image.url) ? 'active' : ''"
								>
									<div class="card shadow-sm border bg-light p-2 text-secondary">
										<img class="img-fluid rounded" :src="image.url" :alt="image.name" />
										<div class="mt-2 text-truncate">
											<span>{{ image.name }}</span>
										</div>
										<span
											@click.stop="removeChooseFile(image.url)"
											class="position-absolute top-0 end-0"
										>
											<!-- <i class="bi bi-dash-square-dotted"></i>
											<i class="bi bi-check-square-fill"></i> -->
										</span>
									</div>
								</div>
							</div>
						</div>

						<div class="d-flex justify-content-center my-4">
							<a-pagination
								v-model:current="pagination.current"
								@change="onChangePage"
								:showQuickJumper="pagination.showQuickJumper"
								:total="pagination.total"
								:pageSizeOptions="['30', '50', '80', '100', '200']"
								:defaultPageSize="30"
								:pageSize="pagination.pageSize"
							/>
						</div>
					</div>
					<div class="col-3 overflow-auto" style="max-height: 65vh; overflow-y: auto">
						<div class="p-3" v-if="imageInfo">
							<h2 class="fs-5 mb-2 text-uppercase">Chi tiết tệp đính kèm</h2>
							<img
								class="img-fluid rounded border"
								:src="resizeImage(imageInfo.url)"
								:alt="imageInfo.name"
							/>
							<div class="mt-2">
								<span class="fw-bold text-primary">{{ imageInfo.name }}</span>
							</div>
							<div class="d-flex mt-2">
								<button class="btn btn-danger btn-sm me-2" @click="handleDeleteFile(imageInfo.url)">
									<i class="bi bi-trash3-fill"></i>
									<!-- <i class="bi bi-check-all"></i> -->
									Xóa
								</button>
							</div>
							<div class="mt-3 border-top pt-2">
								<input type="text" class="form-control" v-model="imageInfo.url" readonly />
							</div>
							<button
								class="btn btn-primary position-fixed"
								style="bottom: 30px; right: 40px"
								@click="handleSaveFile"
							>
								<i class="bi bi-floppy-fill"></i>
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { computed, reactive, ref, watch } from 'vue'
import { useRoute } from 'vue-router'
import { resizeImage } from '@/utils/helpers'

import useAction from '@/composables/useAction'
import { message } from 'ant-design-vue'

const route = useRoute()
const routePath = computed(() => route.path)
const errors = ref({})
const dragging = ref(false)
const top = ref(window.innerHeight / 2)
const left = ref(window.innerWidth / 2)
const mouseX = ref(0)
const mouseY = ref(0)
const selectedFile = ref([])
const selectedFileInput = ref([])
const fileInputRef = ref(null)
const dataImage = ref(null)
const imageInfo = ref(null)

const pagination = reactive({
	current: 1,
	pageSize: 30,
	total: 0,
	showQuickJumper: true,
})

const { getAll, data, messages, loading, create, destroy } = useAction()

const props = defineProps({
	isVisible: {
		type: Boolean,
		default: false,
	},
	multiple: {
		type: Boolean,
		default: false,
	},
})

const visible = ref(props.isVisible)

const emits = defineEmits(['onClose', 'onSelected'])
watch(
	() => props.isVisible,
	(newVal) => (visible.value = newVal)
)

watch(visible, () => {
	if (visible.value) fetchData()
})

const onChangePage = (page, pageSize) => fetchData({ page: page, pageSize: pageSize })

const handleUploadFile = async (event) => {
	try {
		if (!event.target.files || event.target.files.length === 0) {
			throw new Error('No files selected')
		}

		const files = event.target.files
		selectedFileInput.value = files

		const pathArray = routePath.value?.split('/')
		if (!pathArray || pathArray.length === 0 || !pathArray[1]) {
			throw new Error('Invalid URL')
		}

		const folderName = getFolderName(pathArray, files)
		const payload = new FormData()

		Array.from(files).forEach((file) => {
			console.log(file)

			payload.append('files[]', file)
		})

		payload.append('folder_name', folderName)

		const response = await create('uploads', payload)

		if (response) {

			dataImage.value = response.data
			message.success(response.message)
			reloadAll()
		}
	} catch (error) {
		console.error(error)
		message.error('An error occurred. Please try again.')
	}
}

const getFolderName = (pathArray, files) =>
	`${pathArray[1] || ''}/${files.length > 1 ? 'album' : 'thumb'}`

const handleSaveFile = async () => {
	emits('onSelected', selectedFile.value)
	closeModal()
}

const reloadAll = () => {
	selectedFile.value = []
	selectedFileInput.value = null
	imageInfo.value = null
	errors.value = {}
	centerModal()
}

const closeModal = () => {
	reloadAll()
	emits('onClose')
}

const chooseFile = (image) => {
	const index = selectedFile.value.indexOf(image.url)

	imageInfo.value = image

	if (index > -1) {
		return false
	}

	selectedFile.value = props.multiple ? [...selectedFile.value, image.url] : [image.url]
}

const removeChooseFile = (link) => {
	const index = selectedFile.value.indexOf(link)
	if (index > -1) {
		selectedFile.value.splice(index, 1)
	}
	if (selectedFile.value.length === 0) {
		imageInfo.value = null
	}
}

const centerModal = () => {
	top.value = window.innerHeight / 2
	left.value = window.innerWidth / 2
}

const enableDrag = (event) => {
	dragging.value = true
	mouseX.value = event.clientX - left.value
	mouseY.value = event.clientY - top.value
	window.addEventListener('mousemove', onMove)
	window.addEventListener('mouseup', stopDrag)
}

const startDrag = (event) => {
	if (!dragging.value) return
	mouseX.value = event.clientX - left.value
	mouseY.value = event.clientY - top.value
	window.addEventListener('mousemove', onMove)
	window.addEventListener('mouseup', stopDrag)
}

const stopDrag = () => {
	dragging.value = false
	window.removeEventListener('mousemove', onMove)
	window.removeEventListener('mouseup', stopDrag)
}

const onMove = (event) => {
	if (dragging.value) {
		top.value = event.clientY - mouseY.value
		left.value = event.clientX - mouseX.value
	}
}

const handleDeleteFile = async (url) => {
	const payload = {
		url,
	}

	const response = await destroy('uploads', 1, payload)

	dataImage.value = response.data

	message.success(messages.value)

	reloadAll()
}

const fetchData = async (payload = null) => {
	await getAll('uploads', payload)
	if (!data.value) return

	pagination.total = data.value.pagination.totalPages
	pagination.pageSize = data.value.pagination.perPage
	pagination.current = data.value.pagination.currentPage
	dataImage.value = data.value.data
	reloadAll()
}
</script>

<style scoped>
.cursor-move {
	cursor: move;
}
.spin-mask {
	position: absolute;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	z-index: 1000;
	display: flex;
	justify-content: center;
	align-items: center;
	background: rgba(0, 0, 0, 0.05);
}
.bg-modal {
	background-color: rgba(0, 0, 0, 0.4);
	position: fixed;
	top: 0;
	left: 0;
	width: 100%;
	height: 100%;
	z-index: 1000;
}
.header {
	user-select: none;
}
.container-fluid {
	width: 100%;
	height: calc(100vh - 100px);
	background-color: #fff;
	border-radius: 6px;
	overflow: hidden;
	transition: top 0.1s ease, left 0.1s ease; /* Smooth transition for dragging */
}
.tabs-dropdown:hover {
	background-color: #f5f5f5;
}

::-webkit-scrollbar {
	width: 5px;
}
::-webkit-scrollbar-track {
	background: #f1f1f1;
}
::-webkit-scrollbar-thumb {
	background: #9999;
	border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
	background: #999;
}
.card-image {
	position: relative;
	transition: box-shadow 0.2s ease-in-out;
	cursor: pointer;
	border: 2px solid transparent;
	user-select: none;
}
.card-image img {
	user-select: none;
	pointer-events: none;
	object-fit: contain;
}
.card-image::before {
	position: absolute;
	content: '';
	right: 0px;
	display: block;
	height: 25px;
	width: 25px;
	background-color: transparent;
	border-radius: 4px;
	z-index: 10;
}

.card-image.active {
	border-color: #3b74f0dc;
	border-radius: 4px;
}

.card-image.active::before {
	position: absolute;
	/* content: '\2713'; */
	content: '✓';
	font-family: inherit;
	right: 0px;
	display: flex;
	justify-content: center;
	align-items: center;
	height: 25px;
	width: 25px;
	color: #fff; /* Màu của icon */
	font-size: 14px; /* Kích thước của biểu tượng */
	border-radius: 2px;
	z-index: 10;
	background-color: #3b74f0dc;
}
.card-image:hover {
	box-shadow: 3px 1px 15px rgba(0, 0, 0, 0.1);
}
.delete-choose-file {
	position: absolute;
	bottom: -5px;
	right: 3px;
	color: #fff;
	font-size: 18px;
	cursor: pointer;
	font-weight: bolder;
	transform: scale(1);
	display: none;
}
.card-image.active .delete-choose-file {
	display: block;
}
.delete-choose-file .fa-check {
	font-size: 15px;
}
.delete-choose-file .fa-minus {
	display: none;
}

.delete-choose-file:hover .fa-check {
	display: none;
}
.delete-choose-file:hover .fa-minus {
	display: inline;
}
.container-scroll {
	height: calc(100vh - 210px);
}
</style>
