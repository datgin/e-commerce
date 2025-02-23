<script setup>
import { RouterView } from 'vue-router'
</script>

<template>
	<RouterView />
</template>

<style scoped>
</style>


<!-- <script setup>
import { RouterView } from 'vue-router'
import MasterComponent from './components/layouts/MasterComponent.vue'
import AuthComponent from './components/layouts/AuthComponent.vue'
</script>

<template>
	<RouterView v-slot="{ Component, route }">
		<component :is="route.meta.layout === 'auth' ? AuthComponent : MasterComponent">
			<component :is="Component" />
		</component>
	</RouterView>
</template> -->

