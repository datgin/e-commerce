<template>
	<div class="text-center">
		<label class="switch">
			<input
				type="checkbox"
				@change="handleChangeStatus($event)"
				:checked="props.record['published'] == 1 ? true : false"
			/>
			<span class="slider"></span>
		</label>
	</div>
</template>

<script setup>
import { message } from 'ant-design-vue'
import useAction from '@/composables/useAction'

const { create } = useAction()

const props = defineProps({
	record: Object,
	modelName: String,
})

const handleChangeStatus = async () => {
	const payload = {
		model: props.modelName,
		ids: props.record?.key,
		type: 'changePublished',
	}

	const response = await create('/handle-bulkAction', payload)

	const type = response.success ? 'success' : 'error'

	message[type](response.message || 'Có lỗi từ máy chủ vui lòng liên hệ với quản trị viên.')
}
</script>
