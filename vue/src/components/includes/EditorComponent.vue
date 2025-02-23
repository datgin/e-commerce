<template>
	<div class="form-group" >
		<label v-if="props.label" :for="props.name" :class="props.labelClass">{{ props.label }} </label>
		<textarea
			v-model="value"
			:class="className"
			:placeholder="props.placeholder"
			:id="props.name"
		></textarea>
		<small v-if="errorMessage" class="text-danger fw-bold">{{ errorMessage }}</small>
	</div>
</template>

<script setup>
import { onMounted, defineProps, watch } from 'vue'
import { useField } from 'vee-validate'

let editorInstance = null

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
	className: {
		type: String,
		default: 'form-control',
	},
	placeholder: {
		type: String,
		default: '',
	},
	oldValue: {
		type: [String, Boolean, Number],
		default: '',
	},
})

const { value, errorMessage } = useField(props.name)

onMounted(() => {
	if (!window.CKEDITOR) {
		const script = document.createElement('script')
		script.src = '/ckeditor/ckeditor.js'
		script.onload = () => {
			// eslint-disable-next-line no-undef
			editorInstance = CKEDITOR.replace(props.name, {
				filebrowserBrowseUrl: 'http://127.0.0.1:8000/laravel-filemanager?type=Files',
				filebrowserImageBrowseUrl: 'http://127.0.0.1:8000/laravel-filemanager?type=Images',
				filebrowserUploadUrl: 'http://127.0.0.1:8000/laravel-filemanager/upload?type=Files',
				filebrowserImageUploadUrl: 'http://127.0.0.1:8000/laravel-filemanager/upload?type=Images',
				filebrowserWindowFeatures: 'width=800,height=600,resizable=yes,scrollbars=yes',
			})

			editorInstance.on('change', () => {
				// Cập nhật lại value từ CKEditor vào VeeValidate
				value.value = editorInstance.getData()
			})
		}
		document.head.appendChild(script)
	} else {
		// eslint-disable-next-line no-undef
		editorInstance = CKEDITOR.replace(props.name, {
			filebrowserBrowseUrl: 'http://127.0.0.1:8000/laravel-filemanager?type=Files',
			filebrowserImageBrowseUrl: 'http://127.0.0.1:8000/laravel-filemanager?type=Images',
			filebrowserUploadUrl: 'http://127.0.0.1:8000/laravel-filemanager/upload?type=Files',
			filebrowserImageUploadUrl: 'http://127.0.0.1:8000/laravel-filemanager/upload?type=Images',
			filebrowserWindowFeatures: 'width=800,height=600,resizable=yes,scrollbars=yes',
		})

		editorInstance.on('change', () => {
			// Cập nhật lại value từ CKEditor vào VeeValidate
			value.value = editorInstance.getData()
		})
	}
})

watch(
	() => props.oldValue,
	(newOldValue) => {
		if (newOldValue && newOldValue !== undefined && newOldValue !== value.value) {
			value.value = newOldValue
		}
	},
	{ immediate: true }
)
</script>

<style lang="scss" scoped>
</style>
