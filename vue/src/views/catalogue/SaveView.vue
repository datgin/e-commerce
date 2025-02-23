<template>
	<MasterComponent>
		<template #template>
			<PageHeaderComponent :title="state.title" />

			<form class="mt-3" @submit.prevent="onSubmit">
				<div class="row">
					<div class="col-lg-9">
						<a-tabs v-model:activeKey="activeKey" type="card">
							<a-tab-pane key="1" tab="Thông tin danh mục">
								<div class="card">
									<div class="card-body">
										<div class="row">
											<div class="col-lg-2">
												<InputFinderComponent name="image" label="Ảnh" />
											</div>

											<div class="col-lg-10">
												<div class="row">
													<div class="col-lg-6">
														<InputComponent
															label="Tên danh mục"
															name="name"
															type="text"
															placeholder="Nhập tên danh mục"
															required="true"
															classCustom="mb-2"
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
												</div>
											</div>

											<div class="col-lg-12">
												<EditorComponent name="description" label="Bài viết danh mục" />
											</div>
										</div>
									</div>
								</div>
							</a-tab-pane>

							<!-- Tab 2: SEO danh mục -->
							<a-tab-pane key="2" tab="SEO danh mục">
								<div class="card">
									<div class="card-body">
										<div class="row">
											<div class="col-lg-12">
												<InputComponent
													label="Tiêu đề SEO"
													name="seo_title"
													type="text"
													placeholder="Nhập tiêu đề SEO"
													classCustom="mb-2"
												/>

												<InputComponent
													label="Mô tả SEO"
													name="seo_description"
													typeInput="textarea"
													placeholder="Nhập mô tả SEO"
													classCustom="mb-2"
													rows="4"
													maxlength="150"
												/>

												<SelectComponent
													label="Từ khóa SEO"
													name="seo_keywords"
													type="text"
                          mode="tags"
													placeholder="Nhập từ khóa SEO"
												/>
											</div>
										</div>
									</div>
								</div>
							</a-tab-pane>
						</a-tabs>
					</div>

					<div class="col-lg-3 mt-4-5">
						<div class="card">
							<div class="card-body">
								<SelectComponent
									name="parent_id"
									label="Danh mục cha"
									placeholder="Chọn danh mục cha"
									:options="options"
								/>
							</div>
						</div>

						<a-button html-type="submit" class="mt-3" :loading="state.loading" type="primary">
							<i class="bi bi-floppy2 me-2"></i>Lưu thay đổi
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
	InputComponent,
	InputFinderComponent,
	PageHeaderComponent,
	SelectComponent,
	EditorComponent,
} from '@/components/includes'

import { computed, onMounted, reactive, ref } from 'vue'
import * as Yup from 'yup'
import { useForm } from 'vee-validate'
import useAction from '@/composables/useAction'
import { getFirstError } from '@/utils/helpers'
import { message } from 'ant-design-vue'
import { useRouter, useRoute } from 'vue-router'


const state = reactive({
	title: 'Thêm mới danh mục',
	apiCreate: '/catalogues',
	loading: false,
})

const activeKey = ref('1') // Mặc định là tab "Thông tin danh mục"

const { getAll, getOne, create, error, data, update } = useAction()

const router = useRouter()
const route = useRoute()

const id = computed(() => route.params.id || null)

const options = ref([])

const schema = Yup.object({
	name: Yup.string().required('Tên không được để trống!'),
	image: Yup.string().nullable(),
	seo_title: Yup.string().nullable(),
	seo_description: Yup.string().nullable(),
	seo_keywords: Yup.array().nullable(),
})

const { handleSubmit, setValues } = useForm({
	validationSchema: schema,
})

const onSubmit = handleSubmit(async (values) => {
// console.log(values);

	try {
		state.loading = true
		const response = id.value
			? await update(state.apiCreate, id.value, values)
			: await create(state.apiCreate, values)

		if (!response) {
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

	const { name, slug, parent_id, image, seo_title, seo_description, seo_keywords } = data.value.data

	setValues({
		name,
		slug,
		parent_id,
		image,
		seo_title,
		seo_description,
		seo_keywords,
	})
}

onMounted(async () => {
	if (id.value) fetchOne()

	const response = await getAll('/catalogues?catalogues=true')

	options.value = response.data
})
</script>


<style lang="scss" scoped>
</style>
