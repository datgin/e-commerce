<template>
	<nav class="fixed-top" :class="{ headerStyle: !props.toggleSidebar }">
		<div class="d-flex justify-content-between align-items-center">
			<div class="header-icon" @click="toggle">
				<i class="bi bi-list fs-5 fw-bold"></i>
			</div>
			<div class="header-icon d-flex align-items-center">
				<i class="bi bi-arrows-angle-expand" @click="zoom"></i>
				<div class="dropdown">
					<img
						src="/images/avatar5.png"
						alt=""
						width="40"
						height="40"
						class="img-circle ms-3 dropdown-toggle"
						id="dropdownMenuButton"
						data-bs-toggle="dropdown"
						aria-expanded="false"
					/>

					<ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
						<li>
							<div class="dropdown-item">
								<strong>Admin</strong>
								<p>admin@example.com</p>
							</div>
						</li>
						<li class="py-1">
							<a class="dropdown-item" href="#"><i class="bi bi-gear-fill"></i> Cài đặt</a>
						</li>
						<li class="py-1">
							<a class="dropdown-item" href="#"><i class="bi bi-lock-fill"></i> Đổi mật khẩu</a>
						</li>
						<li class="py-1">
							<a class="dropdown-item text-danger" href="#"
								><i class="bi bi-box-arrow-in-right"></i> Đăng xuất</a
							>
						</li>
					</ul>
				</div>
			</div>
		</div>
	</nav>
</template>

<script setup>
const props = defineProps({
	toggleSidebar: Boolean,
	isButtonClick: Function,
})

const emit = defineEmits(['toggleMenu'])
const toggle = () => {
	props.isButtonClick()
	emit('toggleMenu')
}
const zoom = () => {
	// Kiểm tra tính khả dụng của Fullscreen API
	const element = document.documentElement // Lấy toàn bộ trang
	if (element.requestFullscreen) {
		element.requestFullscreen()
	} else if (element.mozRequestFullScreen) {
		// Firefox
		element.mozRequestFullScreen()
	} else if (element.webkitRequestFullscreen) {
		// Chrome, Safari và Opera
		element.webkitRequestFullscreen()
	} else if (element.msRequestFullscreen) {
		// IE/Edge
		element.msRequestFullscreen()
	}
}
</script>

<style scoped>
.dropdown-menu li:not(.dropdown-menu li:last-child) {
	border-bottom: 1px solid rgba(158, 158, 158, 0.5);
}
.dropdown-menu {
	--bs-dropdown-min-width: 15rem;
	background-color: white;
	border: 1px solid #ccc;
	border-radius: 4px;
	box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
}
.img-circle {
	cursor: pointer;
	border-radius: 50%;
	box-shadow: 0 3px 6px rgba(0, 0, 0, 0.16), 0 3px 6px rgba(0, 0, 0, 0.23) !important;
}
.fixed-top {
	transition: 0.3s ease-in-out;
	padding: 5.5px;
	background-color: #fff;
	margin-left: 250px;
	border-bottom: 1px solid #dee2e6;
}
.header-icon {
	padding: 0.5rem 1rem;
}
.bi-list::before,
.bi-arrows-angle-expand::before {
	cursor: pointer;
	font-weight: 700 !important;
}
</style>
