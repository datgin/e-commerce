<script setup>
import { onMounted, ref, h } from 'vue'
import 'datatables.net-dt/css/dataTables.dataTables.css'
import $ from 'jquery'
import 'datatables.net'
import { useRouter } from 'vue-router'
import { Modal, message } from 'ant-design-vue'
import axiosInstance from '@/configs/axios'
import { getFirstError } from '@/utils/helpers'

const router = useRouter()
const selectedIds = ref([])
const tableRef = ref(null)

const props = defineProps({
	columns: Array,
	fetchUrl: String,
	model: String,
})

let table

onMounted(() => {
	table = $(tableRef.value).DataTable({
		processing: true,
		serverSide: true,
		ajax: {
			url: `http://localhost:8000/api/${props.fetchUrl}`,
			type: 'GET',
			beforeSend: function (xhr) {
				const token = localStorage.getItem('token') // Lấy token từ localStorage
				if (token) {
					xhr.setRequestHeader('Authorization', `Bearer ${token}`)
				}
			},
			error: function (xhr) {
				if (xhr.status === 401) {
					// Nếu lỗi 401 Unauthorized
					console.log('Token hết hạn, cần refresh token')

					// refreshTokenAndRetry()
				}
			},
		},
		columns: [
			{
				data: 'checkbox',
				title: '<input type="checkbox" class="form-check-input" id="select-all">',
				orderable: false,
				searchable: false,
				width: '5px',
				className: 'text-center',
			},
			...props.columns,
		],
		language: {
			lengthMenu: '_MENU_',
			zeroRecords: 'Không tìm thấy dữ liệu',
			info: 'Đang hiển thị _START_ đến _END_ của _TOTAL_ mục',
			infoEmpty: 'Không có dữ liệu',
			infoFiltered: '(được lọc từ _MAX_ mục)',
			search: '',
		},
		initComplete: function () {
			$('input[type="search"]').attr('placeholder', 'Tìm kiếm...')
		},
		createdRow: function (row, data) {
			$(row).attr('data-id', data.id)
		},
		drawCallback: function () {
			handleEditButtonClick()
		},
		order: [],
	})

	// function refreshTokenAndRetry() {
	// 	const refreshToken = localStorage.getItem('refresh_token') // Lấy refresh token
	// 	if (!refreshToken) {
	// 		alert('Phiên đăng nhập đã hết hạn, vui lòng đăng nhập lại.')
	// 		return
	// 	}

	// 	$.ajax({
	// 		url: 'http://localhost:8000/api/refresh-token', // API làm mới token
	// 		type: 'POST',
	// 		headers: { Authorization: `Bearer ${refreshToken}` },
	// 		success: function (response) {
	// 			localStorage.setItem('token', response.access_token) // Cập nhật token mới
	// 			localStorage.setItem('refresh_token', response.refresh_token) // Cập nhật refresh token mới

	// 			// Reload DataTable để gửi request lại với token mới
	// 			table.ajax.reload(null, false)
	// 		},
	// 		error: function () {
	// 			alert('Không thể làm mới phiên đăng nhập, vui lòng đăng nhập lại.')
	// 			localStorage.removeItem('token')
	// 			localStorage.removeItem('refresh_token')
	// 		},
	// 	})
	// }

	function handleEditButtonClick() {
		$('.btn-edit').on('click', function (event) {
			event.preventDefault()
			let redirectUrl = $(this).data('url')
			router.push(redirectUrl)
		})
	}

	let targetElement = document.querySelector('.dt-length')

	if (targetElement && !document.querySelector('#action-select')) {
		let selectElement = document.createElement('select')
		selectElement.innerHTML = `
        <option value="">Chọn hành động</option>
        <option value="1">Xóa tất cả</option>
        <option value="2">Cập nhật trạng thái</option>
      `
		selectElement.classList.add('dt-input', 'w-auto', 'ms-2')

		selectElement.style.display = 'none'
		;(selectElement.id = 'action-select'),
			targetElement.insertAdjacentElement('afterend', selectElement)
	}

	$(document)
		.off('click', '#select-all')
		.on('click', '#select-all', function () {
			let checked = $(this).is(':checked')
			let selected = []

			$('.row-checkbox').each(function () {
				$(this).prop('checked', checked)
				if (checked) {
					selected.push($(this).val())
				}
			})

			selectedIds.value = selected

			$('.row-checkbox').each(function () {
				$(this).prop('checked', checked)
				let row = $(this).closest('tr')

				if (checked) {
					row.find('td').css('background-color', '#E5E7E8') // Đổi màu nền
				} else {
					row.find('td').css('background-color', '') // Trả về mặc định
				}
			})

			$('#action-select').toggle(selectedIds.value.length > 0)
		})

	// Hủy bỏ sự kiện cũ trước khi đăng ký mới
	$(document)
		.off('change', '.row-checkbox')
		.on('change', '.row-checkbox', function () {
			let selected = []

			$('.row-checkbox:checked').each(function () {
				selected.push($(this).val())
			})

			selectedIds.value = selected

			$('#action-select').toggle(selectedIds.value.length > 0)

			let row = $(this).closest('tr')

			if ($(this).is(':checked')) {
				row.find('td').css('background-color', '#E5E7E8') // Thêm màu nền vào từng ô
			} else {
				row.find('td').css('background-color', '') // Trả lại màu mặc định
			}
		})

	document.addEventListener('change', async (event) => {
		if (event.target.classList.contains('toggle-publish')) {
			const id = event.target.dataset.id
			const checked = event.target.checked
			console.log(`Cập nhật published của ID ${id} thành: ${checked}`)

			const payload = {
				model: props.model,
				ids: id,
				type: 'changePublished',
			}

			const response = await axiosInstance.post('/handle-bulkAction', payload)

			const type = response.data.success ? 'success' : 'error'

			message[type](
				response.data.message || 'Có lỗi từ máy chủ vui lòng liên hệ với quản trị viên.'
			)
		}
	})

	$(document)
		.off('change', '#action-select')
		.on('change', '#action-select', function () {
			let action = $(this).val()

			if (action === '1') {
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
			} else if (action === '2') {
				onChangePublished()
			}
		})

	const onDestroy = async () => {
		let actionSelect = $('#action-select')
		try {
			const respone = await axiosInstance.post('/handle-bulkAction', {
				ids: selectedIds.value,
				model: props.model,
				type: 'forceDelete',
			})

			actionSelect.css('display', 'none')
			actionSelect.val('')

			selectedIds.value = []

			$('.row-checkbox').prop('checked', false)
			$('#select-all').prop('checked', false)

			table.draw()
			message.success(respone.data.message)
		} catch (error) {
			message.error(error.response.data.message || getFirstError(error.response.data.errors))
		}
	}

	const onChangePublished = async () => {
		let actionSelect = $('#action-select')
		try {
			const respone = await axiosInstance.post('/handle-bulkAction', {
				ids: selectedIds.value,
				model: props.model,
				type: 'changePublished',
			})

			actionSelect.css('display', 'none')
			actionSelect.val('')

			selectedIds.value = []
			$('#select-all').prop('checked', false)

			table.draw()

			message.success(respone.data.message)
		} catch (error) {
			message.error(error.response.data.message || getFirstError(error.response.data.errors))
		}
	}
})
</script>

<template>
	<div class="card">
		<div class="card-body">
			<div class="table-responsive">
				<table ref="tableRef" id="myTable" class="table table-hover"></table>
			</div>
		</div>
	</div>
</template>



