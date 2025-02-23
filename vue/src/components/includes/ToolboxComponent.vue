<template>
	<a-page-header class="my-3" style="border: 1px solid rgb(235, 237, 240); background-color: #fff">
		<a-row>
			<a-col :span="18" class="d-flex gap-3">
				<a-select
					size="large"
					ref="select"
					v-model:value="filter"
					@change="filterPublish"
					v-if="props.isColumnPublished"
				>
					<a-select-option value="">--- Lọc trạng thái ---</a-select-option>
					<a-select-option value="1">Được xuất bản</a-select-option>
					<a-select-option value="0">Chưa được xuất bản</a-select-option>
				</a-select>

				<a-input-search
					size="large"
					:allowClear="true"
					placeholder="Tìm kiếm tại đây..."
					v-model:value="searchText"
					@change="debouncedSearch"
          style="width: 40%;"
				/>

				<!-- <a-button type="primary" @click="getTrash" danger
					><i class="bi bi-trash-fill me-2"></i>Thùng rác</a-button
				> -->
        <a-select
					class="ms-2"
					v-if="showOption"
					ref="select"
					v-model:value="action"
					@change="handleChange"
					size="large"
				>
					<a-select-option value="">--- Chọn hành động ---</a-select-option>
					<a-select-option value="destroy">Xóa tất cả</a-select-option>
					<a-select-option value="change" v-if="props.isColumnPublished"
						>Thay đổi trạng thái</a-select-option
					>
					<!-- <a-select-option v-if="!showOptionRestore" value="soft_delete"
						>Di chuyển vào thùng rác</a-select-option
					>
					<a-select-option v-else value="restore">Khôi phục</a-select-option> -->
				</a-select>
			</a-col>

			<a-col :span="6" class="d-flex justify-content-end align-items-center">


				<!-- <span class="ms-2 me-3">
					<template v-if="showOption">
						{{ `Đã chọn ${props.ids.length} mục` }}
					</template>
				</span> -->

				<a-button class="btn-reload" :loading="loading" type="primary" @click="reLoad"
					><i class="bi bi-arrow-clockwise me-2"></i>Tải lại trang</a-button
				>
			</a-col>
		</a-row>
	</a-page-header>
</template>

<script setup>
import { ref, defineProps, h, watch } from 'vue'
import { Modal, message } from 'ant-design-vue'

import axiosInstance from '@/configs/axios'

const props = defineProps({
	ids: [Object, Array],
	model: {
		type: String,
		required: true,
	},
	updateTitle: Function,
	submited: Boolean,
	isColumnPublished: {
		type: Boolean,
		default: true,
	},
})

const showOptionRestore = ref(false)
const searchText = ref('')
const showOption = ref(false)
const action = ref('')
const filter = ref('')
const loading = ref(false)

watch(
	() => props.submited,
	() => {
		if (props.submited == true) reLoad()
	},
	{ immediate: true }
)

watch(
	() => props.ids,
	() => {
		showOption.value = props.ids.length > 0
	},
	{ deep: true }
)

const emit = defineEmits(['search', 'fetch', 'resetChecked'])

const onSearch = () => {
	emit('search', searchText.value, filter.value, showOptionRestore.value)
}

function debounce(fn, delay) {
	let timer = null // Khởi tạo biến timer
	return function (...args) {
		clearTimeout(timer) // Xóa timer cũ
		timer = setTimeout(() => {
			fn(...args) // Gọi hàm sau delay
		}, delay)
	}
}

// const getTrash = () => {
// 	if (showOptionRestore.value == true) return

// 	showOptionRestore.value = true

// 	props.updateTitle(showOptionRestore.value)

// 	emit('fetch', {
// 		filter: filter.value,
// 		search: searchText.value,
// 		deleted_at: showOptionRestore.value,
// 	})

// 	emit('resetChecked')
// }

const reLoad = () => {
	loading.value = true
	searchText.value = ''
	showOption.value = false
	action.value = ''
	filter.value = ''
	emit('fetch')
	emit('resetChecked')

	if (showOptionRestore.value == true) {
		showOptionRestore.value = false

		props.updateTitle(showOptionRestore.value)
	}
	setTimeout(() => {
		loading.value = false
	}, 1000)
}

const debouncedSearch = debounce(onSearch, 500)

const onDestroy = async () => {
	try {
		const respone = await axiosInstance.post('/handle-bulkAction', {
			ids: props.ids,
			model: props.model,
			type: 'forceDelete',
		})

		showOption.value = false
		emit('fetch', {
			filter: filter.value,
			search: searchText.value,
			deleted_at: showOptionRestore.value,
		})
		message.success(respone.data.message)
	} catch (error) {
		message.success(error.response.data.message)
	} finally {
		action.value = ''
	}
}

const filterPublish = () => {
	emit('fetch', {
		filter: filter.value,
		search: searchText.value,
		deleted_at: showOptionRestore.value,
	})
}

const onChangePublished = async () => {
	try {
		const respone = await axiosInstance.post('/handle-bulkAction', {
			ids: props.ids,
			model: props.model,
			type: 'changePublished',
		})

		showOption.value = false
		emit('resetChecked')
		emit('fetch', {
			filter: filter.value,
			search: searchText.value,
			deleted_at: showOptionRestore.value,
		})
		message.success(respone.data.message)
	} catch (error) {
		console.log(error)
		message.success('Đã có lỗi xảy ra. Vui lòng thử lại sau!')
	} finally {
		action.value = ''
	}
}

const showConfirm = () => {
	Modal.confirm({
		title: 'Bạn có chắc chắn muốn xóa vĩnh viễn?',
		icon: () => h('span', { class: 'anticon anticon-warning', style: 'color: red;' }, '⚠️'),
		content: 'Thao tác này sẽ xóa bản ghi vĩnh viễn! Không thể khôi phục lại được.',
		okText: 'Xóa',
		okType: 'danger',
		cancelText: 'Hủy',
		onOk: onDestroy,
		onCancel() {
			console.log('Đã hủy thao tác xóa.')
		},
	})
}

const handleSoftDelete = async () => {
	try {
		const respone = await axiosInstance.post('/handle-bulkAction', {
			ids: props.ids,
			model: props.model,
			type: 'delete',
		})

		emit('resetChecked')
		emit('fetch', {
			filter: filter.value,
			search: searchText.value,
			deleted_at: showOptionRestore.value,
		})
		message.success(respone.data.message)
	} catch (error) {
		console.log(error)
		message.success('Đã có lỗi xảy ra. Vui lòng thử lại sau!')
	} finally {
		action.value = ''
	}
}

const handleRestore = async () => {
	try {
		const respone = await axiosInstance.post('/handle-bulkAction', {
			ids: props.ids,
			model: props.model,
			type: 'restore',
		})

		emit('resetChecked')
		emit('fetch', {
			filter: filter.value,
			search: searchText.value,
			deleted_at: showOptionRestore.value,
		})
		message.success(respone.data.message)
	} catch (error) {
		console.log(error)
		message.success('Đã có lỗi xảy ra. Vui lòng thử lại sau!')
	} finally {
		action.value = ''
	}
}

const handleChange = (value) => {
	if (value == '') return false

	if (value == 'destroy') {
		showConfirm()
	} else if (value == 'change') {
		onChangePublished()
	} else if (value == 'soft_delete') {
		handleSoftDelete()
	} else {
		handleRestore()
	}
}
</script>

<style scoped>
.btn-reload {
	background-color: rgb(80, 70, 70);
}
.btn-reload:hover {
	background-color: rgba(80, 70, 70, 0.8);
}
</style>
