<template>
	<MasterComponent>
		<template #template>
			<PageHeaderComponent :title="state.title" />

			<ToolboxComponent
				@search="onSearch"
				:ids="ids"
				:model="state.modelName"
				@fetch="fetchData"
				@resetChecked="resetSelectedRowKeys"
				:updateTitle="updateTitle"
				:submited="state.submited"
				:isColumnPublished="state.isColumnPublished"
			/>

			<div class="row">
				<div class="col-lg-4">
					<div class="card">
						<div class="card-body">
							<form action="" @submit.prevent="onSubmit">
								<InputComponent label="Tên" name="value" placeholder="Nhập tên" required="true" />

								<InputComponent
									tooltipText="Nếu không nhập sẽ tự động lấy theo tên"
									label="Slug"
									name="slug"
									placeholder="Nhập slug"
								/>

								<InputComponent
									label="Mô tả"
									name="description"
									placeholder="Nhập mô tả"
									typeInput="textarea"
									rows="4"
									maxlength="50"
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
										v-if="column.dataIndex === 'value'"
										@click="handleRowClick(record)"
										class="pointer"
									>
										<a href="javascript:void(0)"> {{ record.value }}</a>
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

import { PageHeaderComponent, ToolboxComponent, InputComponent } from '@/components/includes'

import usePagination from '@/composables/usePagination'

import { columns } from './columns'

import { onMounted, reactive, ref } from 'vue'

import { getFirstError, removeLastTwoWords } from '@/utils/helpers'

import * as Yup from 'yup'
import { useForm } from 'vee-validate'
import { message } from 'ant-design-vue'
import useAction from '@/composables/useAction'
import { useRoute } from 'vue-router'

const { create, error, update, getOne } = useAction()

const selectedRowKey = ref(null)

const route = useRoute()

const {
	resData,
	loading,
	pagination,
	handleTableChange,
	fetchData,
	ids,
	onSelectChange,
	resetSelectedRowKeys,
} = usePagination(`/attribute-values?slug=${route.params.value}`)

const attribute_id = ref(null)

const state = reactive({
	title: 'Danh sách giá trị thuộc tính',
	modelName: 'AttributeValue',
	loading: false,
	submited: false,
	isColumnPublished: false,
	apiCreate: '/attribute-values',
})

onMounted(() => {
	fetchData()
	fetchOne()
})

const fetchOne = async () => {
	const response = await getOne('/attributes', route.params.value)

	if (!response.success) {
		return message.error('loi')
	}

	attribute_id.value = response.data.key

	state.title = `Danh sách giá trị thuộc tính ${response.data.name}`
}

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
		searchField: 'value',
	})
}

const schema = Yup.object({
	value: Yup.string().required('Tên không được để trống!'),
})

const { handleSubmit, resetForm } = useForm({
	validationSchema: schema,
})

const onSubmit = handleSubmit(async (values) => {
	state.loading = true

	values.attribute_id = attribute_id.value

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
		setTimeout(() => {
			state.submited = false
		}, 2000)
	}
})

const handleRowClick = (record) => {
	resetForm({
		values: {
			value: record.value,
			slug: record.slug,
			description: record.description,
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
			value: '',
			slug: '',
			description: '',
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
