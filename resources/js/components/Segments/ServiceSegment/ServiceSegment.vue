<template>
    <div>
        <div class="d-flex justify-content-end mb-2">
            <v-btn color="success" @click="addModal = true" v-if="isAdmin">
                Добавить услугу
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </div>
        <v-simple-table>
            <template slot="default">
                <thead>
                <tr>
                    <th>#</th>
                    <th>Наименование</th>
                    <th>Описание</th>
                    <th>Иконка</th>
                    <th>Изображение</th>
                    <th>Разовые услуги</th>
                    <th v-if="isAdmin">Действие</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="(service, key) of services" :key="service.id">
                    <td>{{++key}}</td>
                    <td>{{ service.name }}</td>
                    <td>
                        <span v-html="service.description"></span>
                    </td>
                    <td class="text-center">
                        <img
                            style="max-width: 25px; height: auto"
                            :src="'../storage/' + service.icon"
                            alt="">
                    </td>
                    <td class="text-center">
                        <img
                            style="max-width: 100px; height: auto"
                            :src="'../storage/' + service.image"
                            alt="">
                    </td>
                    <td>
                        <ul v-if="!service.main_id">
                            <li v-for="(s, key) of additionalServices(service.id)" :key="s.id">
                                {{ s.name }}
                            </li>
                        </ul>
                        <span v-else><b>Разовая услуга</b></span>
                    </td>
                    <td v-if="isAdmin">
                        <v-btn icon @click="deleteId = service.id; deleteModal = true;">
                            <v-icon>mdi-delete</v-icon>
                        </v-btn>
                        <v-btn icon @click="editId = service.id; editModal = true;">
                            <v-icon>mdi-pencil</v-icon>
                        </v-btn>
                    </td>
                </tr>
                </tbody>
            </template>
        </v-simple-table>
        <AddMobileService
            :state="addModal"
            :services="services"
            v-on:onClose="addModal = false"/>
        <AddMobileService
            :state="editModal"
            :services="services"
            :edit-mode="true"
            :id="editId"
            v-on:onClose="editModal = false; editId = null"/>
        <ConfirmationModal
            v-on:cancel="deleteId = null; deleteModal = false;"
            v-on:confirm="deleteService"
            :state="deleteModal"
            message="Вы действительно хотите удалить выбранную услугу?"/>
    </div>
</template>

<script>
    import AddMobileService from "../../Modals/AddMobileService/AddMobileService";
    import ConfirmationModal from "../../Modals/ConfirmationModal/ConfirmationModal";
    import ACTIONS from "../../../store/actions";
    export default {
        components: {
            AddMobileService,
            ConfirmationModal
        },
        props: {
            isAdmin: {
                type: Boolean
            }
        },
        data: () => ({
            addModal: false,
            editId: null,
            deleteModal: false,
            deleteId: null,
            editModal: false,
        }),
        computed: {
            services() {
                return this.$store.getters.getMobileServices;
            },
        },
        methods: {
            additionalServices(id) {
                return this.services.filter(i => i.main_id === id);
            },
            async deleteService() {
                await this.$store.dispatch(ACTIONS.DELETE_MOBILE_SERVICE, this.deleteId);
                this.deleteId = null;
                this.deleteModal = false;
            }
        }
    }
</script>

<style scoped>

</style>
