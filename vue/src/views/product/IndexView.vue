<template>
	<MasterComponent>
		<template #template>
			<PageHeaderComponent :title="state.title" />

			<form class="mt-3" @submit.prevent="onSubmit">
				<div class="row">
					<div class="col-lg-9">
						<div class="card">
							<div class="card-body">
								<EditorComponent name="description" />
							</div>
						</div>
					</div>

					<div class="col-lg-3">
						<div class="card mb-3">
							<div class="card-body">
								<InputFinderComponent name="logo" />
							</div>
						</div>

						<a-button html-type="submit" class="mt-3" :loading="state.loading" type="primary"
							><i class="bi bi-floppy2 me-2"></i>Lưu thay đổi
						</a-button>
					</div>
				</div>
			</form>
		</template>
	</MasterComponent>
</template>

<script setup>
import MasterComponent from '@/components/layouts/MasterComponent.vue'

import {
	PageHeaderComponent,
	InputFinderComponent,
	EditorComponent,
} from '@/components/includes'

import { reactive } from 'vue'
import * as Yup from 'yup'
import { useForm } from 'vee-validate'

const state = reactive({
	title: 'Thêm mới sản phẩm',
	apiCreate: '/products/',
	loading: false,
})

const schema = Yup.object({
	// name: Yup.string().required('Tên không được để trống!'),
})

const { handleSubmit } = useForm({
	validationSchema: schema,
})

const onSubmit = handleSubmit(async (values) => {
	console.log(values)
})
</script>
