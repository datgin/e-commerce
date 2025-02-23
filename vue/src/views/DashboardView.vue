<script setup>
import DataTable from '@/components/DataTable.vue'
import MasterComponent from '@/components/layouts/MasterComponent.vue'
import { onMounted } from 'vue'
import useAction from '@/composables/useAction'
import columns from './columns.js'

const { getAll } = useAction()
const getData = async () => {
	const response = await getAll('/me')
	console.log(response)
}

onMounted(() => {
	getData()
})

</script>

<template>
	<MasterComponent>
		<template #template>
			<h1>Danh sách sản phẩm</h1>
			<DataTable :columns="columns" fetchUrl="users" model="User" />
		</template>
	</MasterComponent>
</template>
