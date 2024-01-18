import "./bootstrap";
import "primeflex/primeflex.css";
import "primevue/resources/themes/lara-light-blue/theme.css";
import "primevue/resources/primevue.min.css";
import "primeicons/primeicons.css";
import "../sass/app.scss";

import { createApp } from "vue";
import VueAxios from "vue-axios";
import axios from "axios";
import PrimeVue from "primevue/config";
import Toast from "primevue/toast";
import ToastService from "primevue/toastservice";
import DialogService from "primevue/dialogservice";
import Dialog from "primevue/dialog";
import DataTable from "primevue/datatable";
import Column from "primevue/column";
import InputText from "primevue/inputtext";
import InputNumber from "primevue/inputnumber";
import Button from "primevue/button";
import Toolbar from "primevue/toolbar";
import ToggleButton from "primevue/togglebutton";
import App from "./App.vue";

const app = createApp(App);

app.use(VueAxios, axios);
app.use(PrimeVue);
app.use(ToastService);
app.use(DialogService);

app.component("Toast", Toast);
app.component("ToggleButton", ToggleButton);
app.component("Dialog", Dialog);
app.component("DataTable", DataTable);
app.component("Column", Column);
app.component("InputText", InputText);
app.component("InputNumber", InputNumber);
app.component("Button", Button);
app.component("Toolbar", Toolbar);

app.mount("#app");
