<script setup>
import { ref, onMounted, computed, watch } from 'vue'
import { useRoute } from 'vue-router'
import sidebarData from '@/data/sidebar.json' // Import file sidebar.json

const props = defineProps({
	toggleSidebar: Boolean,
	hoverSidebar: Function,
	isButtonClick: Boolean,
})

watch(
	() => props.toggleSidebar,
	(newValue) => console.log(newValue)
)

const sidebarStyle = computed(() => {
	return {
		width: props.toggleSidebar ? '250px' : '74px', // Dựa vào toggleSidebar để thay đổi width
		height: '100vh', // Luôn luôn giữ height là 100vh
	}
})

const route = useRoute()

// Quản lý trạng thái mở rộng của các menu
const openMenus = ref([])

// Khôi phục trạng thái menu từ localStorage khi reload trang
onMounted(() => {
	const storedOpenMenus = JSON.parse(localStorage.getItem('openMenus'))
	if (storedOpenMenus) {
		openMenus.value = storedOpenMenus
	}
})

const toggleMenu = (menu) => {
	if (openMenus.value.includes(menu)) {
		openMenus.value = openMenus.value.filter((m) => m !== menu)
	} else {
		openMenus.value = [menu] // Đảm bảo chỉ có một mục mở
	}
	localStorage.setItem('openMenus', JSON.stringify(openMenus.value))
}

const isOpen = (menu) => openMenus.value.includes(menu)

// Xác định trạng thái active dựa trên URL
const isActive = (path) => route.path === path
</script>

<template>
	<div
		class="d-flex flex-column flex-shrink-0 bg-dark position-fixed text-light"
		:style="sidebarStyle"
		@mouseenter="props.isButtonClick && props.hoverSidebar()"
		@mouseleave="props.isButtonClick && props.hoverSidebar()"
	>
		<router-link to="/admin" class="">
			<h4 class="brand-link border-bottom mb-0">
				<img class="brand-image img-circle elevation-3" src="/images/AdminLTELogo.png" alt="" />
				<span :style="{ opacity: props.toggleSidebar ? 1 : 0 }" class="sidebar-text"
					>LARAVEL SHOP</span
				>
			</h4>
		</router-link>

		<ul class="nav nav-pills flex-column mb-auto p-2">
			<li v-for="item in sidebarData" :key="item.name" class="nav-item">
				<router-link
					v-if="item.children.length === 0"
					:to="item.route"
					class="nav-link"
					@click.prevent="toggleMenu('')"
					:class="{ active: isActive(item.route) }"
				>
					<i :class="item.icon"></i>
					<span v-if="props.toggleSidebar">{{ item.name }}</span>
				</router-link>
				<a v-else href="#" class="nav-link" @click.prevent="toggleMenu(item.name)">
					<i :class="item.icon"></i>
					<span v-if="props.toggleSidebar">{{ item.name }}</span>
					<i
						v-if="props.toggleSidebar"
						class="bi bi-caret-right-fill float-end"
						:class="{ rotated: isOpen(item.name) }"
					></i>
				</a>

				<ul v-if="props.toggleSidebar" class="submenu" :class="{ expanded: isOpen(item.name) }">
					<li v-for="child in item.children" :key="child.route">
						<router-link
							:class="{ active: isActive(`/admin/${child.route}`) }"
							:to="`/admin/${child.route}`"
							class="nav-link"
						>
							<i class="bi bi-dot"></i>
							<span v-if="props.toggleSidebar">{{ child.name }}</span>
						</router-link>
					</li>
				</ul>
			</li>
		</ul>
	</div>
</template>

<style scoped>
.d-flex {
	transition: width 0.3s ease, opacity 0.3s ease, visibility 0s linear 0.3s;
}
.nav-link {
	padding: 8px 20px;
	white-space: nowrap;
}
.nav-item p {
	display: inline;
}
.bi-caret-right-fill {
	transition: transform 0.3s ease;
}
.bi-caret-right-fill.rotated {
	transform: rotate(90deg);
}
.bg-dark {
	background-color: #343a40 !important;
	transition: 0.3s ease-in-out;
}
.active {
	color: #ffffff !important;
	background-color: rgba(255, 255, 255, 0.1) !important;
}
.submenu {
	overflow: hidden;
	max-height: 0;
	transition: max-height 0.3s ease, padding 0.3s ease;
	padding-left: 0;
	list-style: none;
}
.submenu.expanded {
	max-height: 200px;
}
.sidebar-text {
	transition: opacity 0.3s ease;
}
</style>
