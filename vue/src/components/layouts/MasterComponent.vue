<template>
	<div>
		<!-- Sidebar luôn hiển thị -->
		<SidebarComponent
			:isButtonClick="isButtonClick"
			:toggleSidebar="toggleSidebar"
			:hoverSidebar="hoverSidebar"
		/>

		<HeaderComponent
			:isButtonClick="handleClick"
			:toggleSidebar="toggleSidebar"
			@toggleMenu="toggleMenu"
		/>
		<!-- Nội dung thay đổi dựa trên Router -->
		<main class="main-content" :class="{ headerStyle: !toggleSidebar }">
			<div class="mt-7">
				<slot name="template"></slot>
			</div>
		</main>
	</div>
</template>

<script setup>
import SidebarComponent from '../layouts/partials/SidebarComponent.vue'
import HeaderComponent from '../layouts/partials/HeaderComponent.vue'
import { ref } from 'vue'

const toggleSidebar = ref(true)

const isButtonClick = ref(false)

const handleClick = () => {
	isButtonClick.value = !isButtonClick.value
}

const toggleMenu = () => {
	toggleSidebar.value = !toggleSidebar.value
}

const hoverSidebar = () => {
	toggleSidebar.value = !toggleSidebar.value ? true : false
}
</script>

<style scoped>
.mt-7 {
	margin-top: 5rem !important;
}
.main-content {
	transition: 0.3s ease-in-out;
	margin-left: 250px; /* Tương ứng với width của sidebar */
	padding: 20px; /* Tạo khoảng cách bên trong */
	background-color: #f8f9fa; /* Tùy chỉnh màu nền */
	min-height: 100vh; /* Đảm bảo chiều cao full màn hình */
}
</style>

