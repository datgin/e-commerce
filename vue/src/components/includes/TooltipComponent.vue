<template>
	<a-tooltip :color="props.color">
		<template #title>
			<div class="w-auto">
				{{ props.title }}
			</div>
		</template>
		<div class="ms-1 d-inline-block cursor-help" :class="props.className">
			<i class="bi bi-info-circle-fill text-primary"></i>
		</div>
	</a-tooltip>
</template>

<script setup>
const props = defineProps({
	title: {
		type: String,
		default: '',
	},
	className: {
		type: String,
		default: ' ',
	},
	color: {
		type: String,
		default: '',
	},
})
</script>

<style scoped>
.cursor-help {
	cursor: help;
}
</style>
