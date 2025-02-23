<template>
	<div class="form-group" :class="props.classCustom">
		<label v-if="props.label" :for="props.name" :class="props.labelClass"
			>{{ props.label }}
			<span v-if="props.required" class="text-danger">(*)</span>
			<TooltipComponent v-if="props.tooltipText" :title="props.tooltipText" />
		</label>
		<input
			v-if="props.typeInput == 'text' && props.type != 'password'"
			v-model="value"
			:class="className"
			:id="props.name"
			:type="props.type"
			:placeholder="props.placeholder"
			:status="errorMessage ? 'error' : ''"
			:allowClear="true"
			:disabled="props.disabled"
			:suffix="props.suffix"
		/>

		<label class="switch" v-if="props.typeInput == 'checkbox'">
			<input
				:id="props.name"
				v-model="value"
				:type="props.typeInput"
				:class="className"
				:checked="props.checked"
			/>
			<span class="slider"></span>
		</label>

		<textarea
			v-if="props.typeInput == 'textarea'"
			v-model="value"
			:class="className"
			:id="props.name"
			:type="props.type"
			:placeholder="props.placeholder"
			:status="errorMessage ? 'error' : ''"
			:allowClear="true"
			:auto-size="true"
			:maxlength="props.maxlength"
			:disabled="props.disabled"
			:cols="props.cols"
			:rows="props.rows"
		></textarea>

		<small v-if="errorMessage" class="text-danger fw-bold">{{ errorMessage }}</small>
	</div>
</template>

<script setup>
import TooltipComponent from '@/components/includes/TooltipComponent.vue'
import { useField } from 'vee-validate'
import { watch } from 'vue'

const props = defineProps({
	classCustom: {
		type: String,
		default: '',
	},
	checked: {
		type: Boolean,
		default: false,
	},
	typeInput: {
		type: String,
		default: 'text',
	},
	note: {
		type: String,
		default: '',
	},
	tooltipText: {
		type: String,
		default: '',
	},
	required: {
		type: [Boolean, String],
		default: false,
	},
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
	type: {
		type: String,
		default: 'text',
	},
	placeholder: {
		type: String,
		default: '',
	},
	suffix: {
		type: String,
		default: '',
	},
	maxlength: {
		type: [String, Number, Boolean],
		default: 0,
	},
	oldValue: {
		type: [String, Boolean, Number],
		default: '',
	},
	disabled: {
		type: Boolean,
		default: false,
	},
	cols: {
		type: [String, Number],
		default: 30,
	},
	rows: {
		type: [String, Number],
		default: 10,
	},
})

const { value, errorMessage } = useField(props.name)


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
