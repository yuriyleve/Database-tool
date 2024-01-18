<template>
    <div class="card">
        <div class="flex flex-column md:flex-row">
            <div class="w-full flex flex-column align-items-center justify-content-center gap-3 py-5">
                <div class="flex align-items-center gap-2">
                    <label>Username</label>
                    <InputText id="username" type="text" v-model.trim="username" required="true" autofocus
                        :class="{ 'p-invalid': isValid }" />
                </div>
                <div class="flex align-items-center gap-2">
                    <label>Password</label>
                    <InputText id="password" type="password" v-model.trim="password" required="true" autofocus
                        :class="{ 'p-invalid': isValid }" />
                </div>
                <Button label="Login" icon="pi pi-user" class="w-10rem" @click="login"></Button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from "vue";

const username = ref("");
const password = ref("");
const isValid = ref(false);

const login = async () => {
    if (username && password) {
        if (username.value === "admin" && password.value === "123$%^") {
            isValid.value = false
            localStorage.setItem("isLogin", "true");
            window.location.reload();
        } else {
            isValid.value = true
        }
    }
};

onMounted(async () => { });
</script>
