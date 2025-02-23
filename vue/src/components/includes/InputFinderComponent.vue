<template>
	<div class="form-group mb-3">
		<div class="image-upload-list" :class="{ 'is-invalid': errorMessage }">
			<label v-if="props.label" :for="props.name" class="d-block" :class="props.labelClass"
				>{{ props.label }}
				<span v-if="props.required" class="text-danger">(*)</span>
			</label>
			<a-space :size="1" class="items-start">
				<a-upload
					v-if="fileList.length"
					v-model:file-list="fileList"
					:before-upload="() => false"
					list-type="picture-card"
					status="error"
					@preview="handlePreview"
					@change="handleSelectFile"
				>
				</a-upload>
				<div @click="isVisibleFileManager = true" class="upload-box" v-if="hanleMultipleFile">
					<i class="bi bi-cloud-upload-fill"></i>
					<div class="mt-2 text-sm">Tải tệp</div>
				</div>
			</a-space>

			<a-modal :open="previewVisible" :title="previewTitle" :footer="null" @cancel="handleCancel">
				<img alt="example" style="width: 100%" :src="previewImage" />
			</a-modal>
			<a-input type="hidden" :name="props.name" v-model:value="value" />

			<small v-if="errorMessage" class="text-danger fw-bold d-block mt-1">{{ errorMessage }}</small>

			<FileManagerComponent
				:isVisible="isVisibleFileManager"
				@onClose="isVisibleFileManager = false"
				@onSelected="handleSelectFile"
				:multiple="props.multipleFile"
			/>
		</div>
	</div>
</template>

<script setup>
import { useField } from 'vee-validate'
import { ref, computed, onMounted, watch } from 'vue'
import { getBase64, getFileNameFromUrl, getImageToAnt, isJSONString } from '@/utils/helpers'
import FileManagerComponent from '@/components/includes/FileManagerComponent.vue'

const isVisibleFileManager = ref(false)
const previewVisible = ref(false)
const previewImage = ref('')
const previewTitle = ref('')
const fileList = ref([])

const props = defineProps({
	label: {
		type: String,
		default: '',
	},
	labelClass: {
		type: String,
		default: 'fw-bold mb-2',
	},
	name: {
		type: String,
		required: true,
	},
	multipleFile: {
		type: [Boolean, String],
		default: false,
	},
})

onMounted(() => {
	watch(
		value,
		() => {
			if (value.value) {
				if (isJSONString(value.value)) {
					fileList.value = getImageToAnt(JSON.parse(value.value))
				} else {
					fileList.value = getImageToAnt(value.value)
				}
			}
		},
		{ immediate: true }
	)
})

watch(fileList, (newFiles) => {
	handleSetFileToValue(newFiles)
})

const hanleMultipleFile = computed(() => {
	if (props.multipleFile) {
		return true
	}
	if (fileList.value.length > 0 && fileList.value.length < 2) {
		return false
	}
	return true
})

const handleSelectFile = (files) => {
	if (files.length > 0) {
		files.forEach((file, index) => {
			if (!fileList.value.some((item) => item.url === file)) {
				const fileName = getFileNameFromUrl(file)
				fileList.value.push({
					uid: fileName,
					name: fileName,
					status: 'done',
					url: file,
				})
			}
		})
	}
	handleSetFileToValue(fileList.value)
}

const handleSetFileToValue = (files) => {
	if (files.length === 0) {
		value.value = ''
	} else {
		const fileUrls = files.map((file) => file.url)
		value.value = props.multipleFile ? JSON.stringify(fileUrls) : fileUrls[0]
	}
}

const handleCancel = () => {
	previewVisible.value = false
	previewTitle.value = ''
}

const handlePreview = async (file) => {
	if (!file.url && !file.preview) {
		file.preview = await getBase64(file.originFileObj)
	}
	previewImage.value = file.url || file.preview
	previewVisible.value = true
	previewTitle.value = file.name
}

const { value, errorMessage } = useField(props.name)
</script>

<style scoped>
.upload-box {
	display: flex;
	justify-content: center;
	align-items: center;
	flex-direction: column;
	width: 100px;
	height: 100px;
	border: 1px dashed #ccc;
	background-color: rgba(0, 0, 0, 0.02);
	border-radius: 5px;
	cursor: pointer;
	transition: all 0.3s ease;
}
.upload-box i {
	font-size: 16px;
}
.image-upload-list.is-invalid .upload-box {
	border-color: #ef4444;
	color: #ef4444;
}
.upload-box:hover {
	border-color: blue;
	color: rgba(0, 0, 0, 0.8) !important;
}

.image-upload-list.is-invalid .upload-box:hover {
	border-color: blue;
}

.ant-upload-select-picture-card .ant-upload-text {
	margin-top: 8px;
	color: #666;
}
.card-image {
	height: 110px !important;
}
</style>
