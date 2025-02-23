<template>
	<div class="auth-layout">
		<div class="login-container">
			<div class="login-box">
				<div class="card card-outline card-primary">
					<div class="card-header text-center">
						<a href="#" class="h3">Ban quản lý</a>
					</div>
					<div class="card-body">
						<p class="login-box-msg text-center">Đăng nhập để bắt đầu phiên của bạn</p>
						<form @submit="onSubmit">
							<div class="form-group mb-3">
								<div class="d-flex">
									<input
										type="email"
										class="form-control rounded-start"
										placeholder="Email"
										v-model="email"
										autocomplete="username"
										v-bind="emailFieldProps"
									/>
									<div class="input-group-append">
										<div class="input-group-text rounded-end">
											<i class="bi bi-envelope-fill"></i>
										</div>
									</div>
								</div>
								<small class="text-danger fw-bold" v-if="errors.email">{{ errors.email }}</small>
							</div>
							<div class="form-group mb-3">
								<div class="d-flex">
									<input
										type="password"
										name="password"
										id="password"
										class="form-control rounded-start"
										placeholder="Password"
										v-model="password"
										autocomplete="current-password"
										v-bind="passwordFieldProps"
									/>
									<div class="input-group-append">
										<div class="input-group-text rounded-end">
											<i class="bi bi-lock-fill"></i>
										</div>
									</div>
								</div>
								<small class="text-danger fw-bold" v-if="errors.password">{{
									errors.password
								}}</small>
							</div>
							<div class="row">
								<div class="col-6">
									<a-button
										:loading="state.loading"
										:disabled="isSubmitting"
										type="primary"
										size="large"
										html-type="submit"
										class="px-4"
									>
										Đăng nhập
									</a-button>
								</div>
							</div>
						</form>
						<p class="mb-1 mt-3">
							<a href="forgot-password.html">Quên mật khẩu</a>
						</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script setup>
import { useForm } from 'vee-validate'
import * as yup from 'yup'
import { message } from 'ant-design-vue'
import { useRouter } from 'vue-router'

import { getFirstError } from '@/utils/helpers'
import { reactive } from 'vue'
import axios from 'axios'

const state = reactive({
	api: 'http://localhost:8000/api/login',
	loading: false,
})

const router = useRouter()

const validationSchema = yup.object({
	email: yup.string().email('Email không hợp lệ').required('Email là bắt buộc'),
	password: yup
		.string()
		.min(6, 'Mật khẩu phải ít nhất 6 ký tự')
		.matches(/[A-Z]/, 'Mật khẩu phải có ít nhất một chữ hoa')
		.matches(/[0-9]/, 'Mật khẩu phải có ít nhất một chữ số')
		.required('Mật khẩu là bắt buộc'),
})

const { defineField, handleSubmit, isSubmitting, errors } = useForm({
	validationSchema, // Đặt schema cho form
})

// Define fields for email and password
const [email, emailFieldProps] = defineField('email')
const [password, passwordFieldProps] = defineField('password')

const onSubmit = handleSubmit(async (values) => {
	state.loading = true

	try {
		const { data } = await axios.post(state.api, values)

		const expiresIn = data.expires_in * 1000
		localStorage.setItem('tokenExpiry', Date.now() + expiresIn)
		localStorage.setItem('token', data.token)
		message.success(data.message)
		router.push({ name: 'dashboard' })
	} catch (error) {
    console.log(error);

		message.error(error.response.data.message || getFirstError(error.response.data.errors))
	} finally {
		state.loading = false
	}
})
</script>


<style  scoped>
.form-control {
	border-radius: 0;
}
.auth-layout {
	display: flex;
	justify-content: center;
	align-items: center;
	min-height: 100vh;
	background-color: #e9ecef;
}
.login-container {
	width: 360px;
}
.input-group-text {
	border-radius: 0;
}
.card-header {
	background-color: transparent;
	border-bottom: 1px solid rgba(0, 0, 0, 0.125);
	padding: 0.75rem 1.25rem;
	position: relative;
	border-top-left-radius: 0.25rem;
	border-top-right-radius: 0.25rem;
}
.card-primary.card-outline {
	border-top: 3px solid #007bff;
}
.px-4{
  border-radius: 4px;
}
</style>
