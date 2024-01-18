<template>
    <div>
        <div class="card">
            <Toolbar class="mb-4">
                <template #start>
                    <Button label="New" icon="pi pi-plus" severity="success" class="mr-2" @click="openNew" />
                    <Button label="Stop all timers" icon="pi pi-stop-circle" severity="info" @click="formateAllTimer" />
                </template>
                <template #center>
                    <Button label="Reset all PlayTime" icon="pi pi-refresh" severity="warning" @click="resetAllTime" />
                </template>
                <template #end>
                    <Button label="Export" icon="pi pi-upload" severity="help" class="mr-2" @click="exportCSV($event)" />
                    <Button label="Logout" icon="pi pi-user" severity="secondary" @click="logout()" />
                </template>
            </Toolbar>

            <DataTable ref="dt" :value="products" v-model:selection="selectedProducts" dataKey="id" :paginator="true"
                :rows="10" :loading="loading" :filters="filters"
                paginatorTemplate="FirstPageLink PrevPageLink PageLinks NextPageLink LastPageLink CurrentPageReport RowsPerPageDropdown"
                :rowsPerPageOptions="[5, 10, 25]"
                currentPageReportTemplate="Showing {first} to {last} of {totalRecords} players">
                <template #header>
                    <div class="flex flex-wrap gap-2 align-items-center justify-content-between">
                        <h4 class="m-0">Manage Players</h4>
                        <span class="p-input-icon-left">
                            <i class="pi pi-search" />
                            <InputText v-model="filters['global'].value" placeholder="Search..." />
                        </span>
                    </div>
                </template>

                <Column field="name" header="Name" sortable></Column>
                <Column field="code" header="Code" sortable></Column>
                <Column field="amount" header="Amount" sortable></Column>
                <Column field="agent" header="Agent" sortable></Column>
                <Column selectionMode="multiple" headerStyle="width: 3rem" header="Blacklist" field="blacklist"
                    :exportable="false"></Column>
                <Column field="playtime" header="Play Time" sortable>
                    <template #body="slotProps">
                        {{ formatTime(slotProps.data.playtime) }}
                    </template>
                </Column>
                <Column header="Timer">
                    <template #body="slotProps">
                        <ToggleButton @click="timer(slotProps.data)" :modelValue="convertNumToBool(slotProps.data.isTime)"
                            onLabel="Turn off" offLabel="Turn on" onIcon="pi pi-times" offIcon="pi pi-check"
                            class="w-8rem" />
                    </template>
                </Column>
                <Column :exportable="false" style="min-width: 8rem">
                    <template #body="slotProps">
                        <Button icon="pi pi-pencil" outlined rounded class="mr-2" @click="editProduct(slotProps.data)" />
                        <Button icon="pi pi-trash" outlined rounded severity="danger"
                            @click="confirmDeleteProduct(slotProps.data)" />
                    </template>
                </Column>
            </DataTable>
        </div>

        <Dialog v-model:visible="productDialog" :style="{ width: '450px' }" header="Player Details" :modal="true"
            class="p-fluid">
            <div class="field">
                <label for="name">Name</label>
                <InputText id="name" v-model.trim="product.name" required="true" autofocus
                    :class="{ 'p-invalid': submitted && !product.name }" />
                <small class="p-error" v-if="submitted && !product.name">Name is required.</small>
            </div>
            <div class="field">
                <label for="code">Code</label>
                <InputText id="code" v-model.trim="product.code" required="true" autofocus
                    :class="{ 'p-invalid': submitted && !product.code }" />
                <small class="p-error" v-if="submitted && !product.code">Code is required.</small>
            </div>
            <div class="field">
                <label for="amount">Amount</label>
                <InputNumber id="amount" :min="0" v-model.trim="product.amount" required="true" autofocus />
            </div>
            <div class="field">
                <label for="agent">Agent</label>
                <InputText id="agent" v-model.trim="product.agent" required="true" autofocus />
            </div>
            <div class="field" v-if="product">
                <label for="playtime">Playtime (minutes)</label>
                <InputNumber id="playtime" :min="0" v-model.trim="product.playtime" autofocus />
            </div>

            <template #footer>
                <Button label="Cancel" icon="pi pi-times" text @click="hideDialog" />
                <Button label="Save" icon="pi pi-check" text @click="saveProduct" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteProductDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                <span v-if="product">Are you sure you want to delete <b>{{ product.name }}</b>?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteProductDialog = false" />
                <Button label="Yes" icon="pi pi-check" text @click="deleteProduct" />
            </template>
        </Dialog>

        <Dialog v-model:visible="deleteProductsDialog" :style="{ width: '450px' }" header="Confirm" :modal="true">
            <div class="confirmation-content">
                <i class="pi pi-exclamation-triangle mr-3" style="font-size: 2rem" />
                <span v-if="product">Are you sure you want to delete the selected players?</span>
            </div>
            <template #footer>
                <Button label="No" icon="pi pi-times" text @click="deleteProductsDialog = false" />
                <Button label="Yes" icon="pi pi-check" text @click="deleteSelectedProducts" />
            </template>
        </Dialog>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import { FilterMatchMode } from "primevue/api";
import { useToast } from "primevue/usetoast";
import PokerService from "../services/PokerService";

const toast = useToast();
const dt = ref();
const today = ref();
const products = ref();
const productDialog = ref(false);
const loading = ref(false);
const deleteProductDialog = ref(false);
const deleteProductsDialog = ref(false);
const product = ref({});
const selectedProducts = ref();
const filters = ref({
    global: { value: null, matchMode: FilterMatchMode.CONTAINS },
});
const submitted = ref(false);

const getToday = () => {
    let d = new Date(),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

    if (month.length < 2) month = "0" + month;
    if (day.length < 2) day = "0" + day;

    today.value = [year, month, day].join("-");
};
const openNew = () => {
    product.value = {};
    submitted.value = false;
    productDialog.value = true;
};
const hideDialog = () => {
    productDialog.value = false;
    submitted.value = false;
};
const saveProduct = async () => {
    submitted.value = true;

    if (product.value.name.trim()) {
        loading.value = true;

        products.value = await PokerService.create(product.value);

        toast.add({
            severity: "success",
            summary: "Successful",
            detail: "Successful",
            life: 3000,
        });

        loading.value = false;
        productDialog.value = false;
        product.value = {};
    }
};
const editProduct = (prod) => {
    product.value = { ...prod };
    productDialog.value = true;
};
const confirmDeleteProduct = (prod) => {
    product.value = prod;
    deleteProductDialog.value = true;
};
const deleteProduct = async () => {
    deleteProductDialog.value = false;
    loading.value = true;

    await PokerService.delete(product.value.id);

    selectedProducts.value = selectedProducts.value.filter(
        (val) => val.id !== product.value.id
    );
    products.value = products.value.filter(
        (val) => val.id !== product.value.id
    );
    product.value = {};
    toast.add({
        severity: "success",
        summary: "Successful",
        detail: "Product Deleted",
        life: 3000,
    });
    loading.value = false;
};
const findIndexByCode = (code) => {
    let index = -1;
    for (let i = 0; i < products.value.length; i++) {
        if (products.value[i].code === code) {
            index = i;
            break;
        }
    }

    return index;
};
const exportCSV = () => {
    dt.value.exportCSV();
};

const convertNumToBool = (num) => {
    return num === 1 ? true : false
};
const logout = () => {
    localStorage.setItem("isLogin", null);
    window.location.reload();
};
const formatTime = (totalMinutes) => {
    const hours = Math.floor(totalMinutes / 60);
    const remainingMinutes = totalMinutes % 60;
    return `${hours.toString().padStart(2, "0")}h:${remainingMinutes
        .toString()
        .padStart(2, "0")}m`;
};

const timer = async (product) => {
    product.isTime = +!product.isTime;

    loading.value = true
    await PokerService.isTime(product);
    loading.value = false

    if (product.isTime === 1) {
        product.timerInterval = setInterval(() => {
            product.playtime++;

            PokerService.timer(product);
        }, 60000);
    } else {
        clearInterval(product.timerInterval);
    }
};
const formateAllTimer = async () => {
    loading.value = true;

    products.value = await PokerService.stopAllTimer();

    loading.value = false;
};
const resetAllTime = async () => {
    loading.value = true;

    products.value = await PokerService.resetAllTime();

    loading.value = false;
};

document.addEventListener("DOMContentLoaded", function () {
    let elements = document.querySelectorAll(
        'div[data-pc-section="headercheckboxwrapper"]'
    );
    elements.forEach(function (element) {
        element.style.display = "none";
    });
});

onMounted(async () => {
    getToday();

    loading.value = true;
    products.value = await PokerService.getPlayers();
    selectedProducts.value = products.value.filter(
        (product) => product.blacklist === 1
    );
    products.value.map(product => {
        if (product.isTime === 1) {
            product.timerInterval = setInterval(() => {
                product.playtime++;

                PokerService.timer(product);
            }, 60000);
        }
    });
    loading.value = false;
});

watch(
    selectedProducts,
    async (newQuestion, oldQuestion) => {
        if (oldQuestion !== undefined) {
            loading.value = true;
            await PokerService.blacklist(selectedProducts.value);
            loading.value = false;
        }
    },
    { immediate: false }
);
</script>
