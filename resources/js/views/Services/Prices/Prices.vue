<template>
    <div>
        <v-card>
            <v-card-title>
                Прайс-лист
            </v-card-title>
            <v-card-text>
                <div class="p-3">
                    <div class="d-flex justify-content-between">
                        <v-btn color='primary'>
                            Экспорт данных
                            <v-icon>mdi-file-excel</v-icon>
                        </v-btn>
                        <v-btn color="primary" @click.stop="addModal = true">
                            Добавить услугу
                            <v-icon class="white--text">mdi-plus</v-icon>
                        </v-btn>
                    </div>
                    <div class="mb-2 d-flex justify-content-end">
                        <v-select
                            style="max-width: 300px"
                            :items="itemFilters"
                            item-text="name"
                            item-value="value"
                            v-model="filterValue"
                        ></v-select>
                    </div>
                    <v-data-table
                        class="mt-2"
                        no-results-text="Нет результатов"
                        no-data-text="Нет данных"
                        :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }"
                        :headers="headers"
                        :items="displayItems"
                    >
                        <template v-slot:item.name="{item}">
                            {{ item.name }}
                            <span v-if="item.main_id"> (Разовая услуга) </span>
                        </template>
                        <template v-slot:item.price="{item}">
                            <span v-if="item.price === null">Не указан</span>
                            <span v-else>{{ item.price}} тенге</span>
                        </template>
                        <template v-slot:item.action="{item}">
                            <v-btn icon text @click.stop="showEditModal(item)">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon text @click.stop="showDeleteModal(item.id)">
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </template>
                        <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                            {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                        </template>
                    </v-data-table>
                </div>
            </v-card-text>
        </v-card>
        <AddService
            :state="addModal"
            :key="modalKey"
            :services="basicServices"
            v-on:onClose="addModal = false; modalKey++"
            v-on:onSave="onSave"/>
        <AddService
            v-if="editModal"
            :key="modalKey + 1"
            title="Редактирование услуги"
            :state="editModal"
            :service="editingService"
            :edit-mode="true"
            :services="basicServices"
            v-on:onClose="closeEditModal"
            v-on:onSave="onEdit"/>
        <ConfirmationModal
            :state="deleteModal"
            message="Вы действительно хотите удалить выбранную услугу"
            v-on:cancel="deleteModal = false"
            v-on:confirm="deleteService"/>
    </div>
</template>

<script>
    import AddService from "../../../components/Modals/AddServiceModal/AddService";
    import showToast from "../../../utils/Toast";
    import ConfirmationModal from "../../../components/Modals/ConfirmationModal/ConfirmationModal";
    import GETTERS from "../../../store/getters";
    import ACTIONS from "../../../store/actions";

    export default {
        components: {ConfirmationModal, AddService},
        data: () => ({
            modalKey: 0,
            editModal: false,
            addModal: false,
            deleteModal: false,
            deleteId: null,
            filterValue: -1,
            itemFilters: [
                {
                    name: 'Все услуги',
                    value: -1,
                },
                {
                    name: 'Обычные услуги',
                    value: 0
                },
                {
                    name: 'Разовые услуги',
                    value: 1
                }
            ],
            items: [
                {
                    title: 'МТК',
                    cost: 3000,
                    isOneTime: false
                },
                {
                    title: 'Автосопровождение',
                    cost: null,
                    isOneTime: true
                }
            ],
            headers: [
                {
                    text: 'Наименование',
                    align: 'left',
                    sortable: false,
                    value: 'name',
                },
                {text: 'Тариф', value: 'price', sortable: false},
                {text: 'Действие', value: 'action', sortable: false},
            ],
            editingService: {},
        }),
        computed: {
            displayItems() {
                if (+this.filterValue === -1) {
                    return this.services;
                }
                if (this.filterValue === 0) {
                    return this.basicServices;
                }
                return this.oneTimeServices;

            },
            services() {
                return this.$store.getters[GETTERS.SERVICES];
            },
            basicServices() {
                return this.services.filter(i => !i.main_id);
            },
            oneTimeServices() {
                return this.services.filter(i => i.main_id);
            }
        },
        methods: {
            onSave() {
                this.addModal = false;
                showToast('Услуга успешно добавлена');
                this.modalKey++;
            },
            closeEditModal() {
                this.editModal = false;
                this.modalKey++;
            },
            onEdit() {
                this.editModal = false;
                this.modalKey++;
                showToast('Услуга успешно изменена');
            },
            async deleteService() {
                await this.$store.dispatch(ACTIONS.DELETE_SERVICE, this.deleteId);
                this.deleteId = null;
                this.deleteModal = false;
                await showToast('Услуга была удалена');
            },
            showDeleteModal(e) {
                this.deleteId = e;
                this.deleteModal = true;
            },
            showEditModal(e) {
                this.editingService = e;
                this.editingService.isOneTime = !!this.editingService.main_id;
                this.editModal = true;
            },
        }
    }
</script>

<style scoped>

</style>
