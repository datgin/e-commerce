<template>
	<a-page-header
		style="border: 1px solid rgb(235, 237, 240); background-color: #ffffff;"
		:title="props.title"
		@back="() => $router.go(-1)"
	>
		<template #extra v-if="props.name">
			<router-link :to="{ name: props.name }" class="btn btn-success btn-sm"
				>Thêm mới (+)</router-link
			>
		</template>
	</a-page-header>
</template>

<script setup>
const props = defineProps({
	title: String,
	name: String,
})
</script>

<style  scoped>
.ant-page-header {
	padding: 10px 24px !important;
}
</style>
