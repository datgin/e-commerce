<template>
	<MasterComponent>
		<template #template>
			<PageHeaderComponent :title="state.title" />

			<form class="mt-3" @submit.prevent="onSubmit">
				<div class="row">
					<div class="col-lg-9">
						<div class="card">
							<div class="card-body">
								<div class="row">
									<div class="col-lg-6">
										<InputComponent
											label="Tên danh mục"
											name="name"
											type="text"
											placeholder="Nhập tên danh mục"
											required="true"
										/>
									</div>

									<div class="col-lg-6">
										<InputComponent
											label="Slug"
											name="slug"
											type="text"
											placeholder="Nhập slug"
											tooltipText="Không nhập sẽ tự động lấy theo tên"
										/>
									</div>

									<div class="col-lg-12">
										<InputComponent
											label="Mô tả"
											name="description"
											type="textarea"
											placeholder="Nhập mô tả"
											typeInput="textarea"
											rows="6"
											maxlength="100"
										/>
									</div>
								</div>
							</div>
						</div>
					</div>

					<div class="col-lg-3">
						<div class="card mb-3">
							<div class="card-body">
								<InputFinderComponent name="logo" />
							</div>
						</div>

						<div class="card mb-3">
							<div class="card-body">
								<InputComponent
									label="Địa chỉ trang web"
									name="website_url"
									type="text"
									placeholder="Nhập địa chỉ trang web"
								/>
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
import { PageHeaderComponent, InputComponent, InputFinderComponent } from '@/components/includes'
import { reactive, computed, onMounted } from 'vue'
import * as Yup from 'yup'
import { useForm } from 'vee-validate'
import { message } from 'ant-design-vue'
import useAction from '@/composables/useAction'
import { useRouter, useRoute } from 'vue-router'
import { getFirstError } from '@/utils/helpers'

const state = reactive({
	title: 'Thêm mới thương hiệu',
	apiCreate: '/brands',
	loading: false,
})

const { getOne, create, error, data, update } = useAction()

const router = useRouter()
const route = useRoute()

const id = computed(() => route.params.id || null)

const schema = Yup.object({
	name: Yup.string().required('Tên không được để trống!'),
})

const { handleSubmit, setValues } = useForm({
	validationSchema: schema,
})

const onSubmit = handleSubmit(async (values) => {
	try {
		state.loading = true
		const response = id.value
			? await update(state.apiCreate, id.value, values)
			: await create(state.apiCreate, values)

		console.log(response)

		if (!response.success) {
			return message.error(getFirstError(error.value.errors))
		}

		message.success(response.message)

		router.go(-1)
	} catch (error) {
		console.log(error)
	} finally {
		state.loading = false
	}
})

const fetchOne = async () => {
	await getOne(state.apiCreate, id.value)

	const { name, slug, logo, website_url } = data.value.data

	setValues({
		name,
		slug,
		logo,
		website_url,
	})
}

onMounted(async () => {
	if (id.value) fetchOne()
})
</script>

<style lang="scss" scoped>
</style>
