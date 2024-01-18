<template>
    <div class="container">
        <data-table v-if="isLogin"></data-table>
        <login v-else></login>
    </div>
</template>

<script setup>
import { ref } from "vue";
import DataTable from "./components/DataTable.vue";
import Login from "./components/Login.vue";

const isLogin = ref(JSON.parse(localStorage.getItem("isLogin")));

window.addEventListener('storage', () => {
    isLogin.value = localStorage.getItem('isLogin');
});
</script>
