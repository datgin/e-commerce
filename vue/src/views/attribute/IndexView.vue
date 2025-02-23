<template>
	<MasterComponent>
		<template #template>
			<PageHeaderComponent :title="state.title" :name="state.createName" />

			<ToolboxComponent
				@search="onSearch"
				:ids="ids"
				:model="state.modelName"
				@fetch="fetchData"
				@resetChecked="resetSelectedRowKeys"
				:updateTitle="updateTitle"
				:submited="state.submited"
			/>

			<div class="row">
				<div class="col-lg-4">
					<div class="card">
						<div class="card-body">
							<form action="" @submit.prevent="onSubmit">
								<InputComponent
									label="Tên thuộc tính"
									name="name"
									placeholder="Nhập tên thuộc tính"
									required="true"
								/>

								<InputComponent
									tooltipText="Nếu không nhập sẽ tự động lấy theo tên"
									label="Slug"
									name="slug"
									placeholder="Nhập slug"
								/>

								<InputComponent
									label="Trạng thái"
									name="published"
									typeInput="checkbox"
									labelClass="mb-0 fw-bold me-2"
									classCustom="d-flex align-content-center"
									:checked="true"
									:oldValue="true"
								/>

								<a-button html-type="submit" :loading="state.loading" class="mt-3" type="primary"
									><i class="bi bi-floppy2 me-2"></i>Lưu thay đổi
								</a-button>

								<a-button html-type="button" class="mt-3 ms-2" @click="resetFormData">
									<i class="bi bi-floppy2 me-2"></i>Làm mới
								</a-button>
							</form>
						</div>
					</div>
				</div>

				<div class="col-lg-8">
					<div class="card">
						<div class="card-body">
							<a-table
								:columns="columns"
								:data-source="resData"
								:loading="loading"
								:pagination="pagination"
								@change="handleTableChange"
								:row-selection="{ selectedRowKeys: ids, onChange: onSelectChange }"
								:row-class-name="rowClassName"
							>
								<template #bodyCell="{ column, record }">
									<div
										v-if="column.dataIndex === 'name'"
										@click="handleRowClick(record)"
										class="pointer"
									>
										<router-link :to="{ name: 'attribute_values', params: { value: record.slug } }">
											{{ record.name }}
										</router-link>
									</div>

									<div v-else-if="column.dataIndex === 'published'">
										<StatusSwitchComponent :record="record" :modelName="state.modelName" />
									</div>
								</template>
							</a-table>
						</div>
					</div>
				</div>
			</div>
		</template>
	</MasterComponent>
</template>

<script setup>
import MasterComponent from '@/components/layouts/MasterComponent.vue'

import {
	PageHeaderComponent,
	ToolboxComponent,
	StatusSwitchComponent,
	InputComponent,
} from '@/components/includes'

import usePagination from '@/composables/usePagination'

import { getFirstError, removeLastTwoWords } from '@/utils/helpers'

import { columns } from './columns'

import { onMounted, reactive, ref } from 'vue'

import * as Yup from 'yup'
import { useForm } from 'vee-validate'
import { message } from 'ant-design-vue'
import useAction from '@/composables/useAction'

const { create, error, update } = useAction()

const selectedRowKey = ref(null)

const {
	resData,
	loading,
	pagination,
	handleTableChange,
	fetchData,
	ids,
	onSelectChange,
	resetSelectedRowKeys,
} = usePagination('/attributes')

const state = reactive({
	title: 'Danh sách thuộc tính',
	apiCreate: '/attributes',
	modelName: 'Attribute',
	loading: false,
	submited: false,
})

onMounted(() => {
	fetchData()
})

const updateTitle = (bool) => {
	state.title = bool == true ? state.title + ' lưu trữ' : removeLastTwoWords(state.title)
}

const onSearch = (searchText, filter, deleted_at) => {
	fetchData({
		current: pagination.current,
		pageSize: pagination.pageSize,
		search: searchText,
		filter: filter,
		deleted_at,
	})
}

const schema = Yup.object({
	name: Yup.string().required('Tên không được để trống!'),
})

const { handleSubmit, resetForm } = useForm({
	validationSchema: schema,
})

const onSubmit = handleSubmit(async (values) => {
	state.loading = true

	try {
		const response = selectedRowKey.value
			? await update(state.apiCreate, selectedRowKey.value, values)
			: await create(state.apiCreate, values)

		if (!response.success) {
			return message.error(getFirstError(error.value.errors))
		}

		message.success(response.message)
		resetFormData()
		state.submited = true
	} catch (error) {
		console.log(error)
	} finally {
		state.loading = false
	}
})

const handleRowClick = (record) => {
	resetForm({
		values: {
			name: record.name,
			slug: record.slug,
			description: record.description,
			published: record.published,
		},
	})

	selectedRowKey.value = record.key
}

const rowClassName = (record) => {
	// Thêm lớp 'disabled-row' nếu bản ghi đã được chọn
	return record.key === selectedRowKey.value ? 'disabled-row' : ''
}

const resetFormData = () => {
	resetForm({
		values: {
			name: '',
			slug: '',
			description: '',
			published: true,
		},
	})

	selectedRowKey.value = null
}
</script>

<style scoped>
.pointer {
	cursor: pointer;
}
</style>
