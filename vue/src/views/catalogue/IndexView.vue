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
			/>

			<div class="card">
				<div class="card-body">
					<a-table
						:columns="columns"
						:data-source="resData"
						:loading="loading"
						:pagination="pagination"
						@change="handleTableChange"
						:row-selection="{ selectedRowKeys: ids, onChange: onSelectChange }"
					>
						<template #bodyCell="{ column, record }">
							<template v-if="column.dataIndex === 'name'">
								<router-link :to="{ name: 'catalogues.update', params: { id: record.key } }">
									{{ record.name }}
								</router-link>
							</template>

							<template v-if="column.dataIndex === 'parent_id'">
								<strong class="text-danger" v-if="record.parent_name == null"> Not Parent </strong>
								<strong class="text-dank" v-else>
									{{ record.parent_name }}
								</strong>
							</template>

							<template v-if="column.dataIndex === 'published'">
								<StatusSwitchComponent :record="record" :modelName="state.modelName" />
							</template>
						</template>
					</a-table>
				</div>
			</div>
		</template>
	</MasterComponent>
</template>

<script setup>
import MasterComponent from '@/components/layouts/MasterComponent.vue'
import { onMounted, reactive } from 'vue'
import { columns } from './columns'
import usePagination from '@/composables/usePagination'
import { removeLastTwoWords } from '@/utils/helpers'

import { ToolboxComponent, PageHeaderComponent, StatusSwitchComponent } from '@/components/includes'

const {
	resData,
	loading,
	pagination,
	handleTableChange,
	fetchData,
	ids,
	onSelectChange,
	resetSelectedRowKeys,
} = usePagination('/catalogues')

const state = reactive({
	title: 'Danh sách danh mục',
	modelName: 'Catalogue',
	createName: 'catalogues.create',
})

const updateTitle = (bool) => {
	state.title = bool == true ? state.title + ' lưu trữ' : removeLastTwoWords(state.title)
}

onMounted(() => {
	fetchData()
})

const onSearch = (searchText) => {
	fetchData({
		current: pagination.current,
		pageSize: pagination.pageSize,
		search: searchText,
	})
}
</script>

<style scoped>
</style>
