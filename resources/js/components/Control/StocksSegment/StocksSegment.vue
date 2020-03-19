<template>
    <div>
        <div class="d-flex justify-content-between">
            <v-btn color="primary" class="button-add" v-if="isAdmin">
                Экспорт данных
                <v-icon>mdi-file-excel-box</v-icon>
            </v-btn>
            <v-btn color="primary" @click="addModal = true" v-if="isAdmin">
                Добавить баннер
                <v-icon>mdi-plus</v-icon>
            </v-btn>
        </div>
        <v-spacer></v-spacer>
        <v-spacer></v-spacer>
        <v-text-field
            v-model="search"
            clearable
            append-icon="mdi-account-search"
            label="Поиск"
            class="mb-2"
            single-line
            hide-details
        ></v-text-field>
        <v-data-table
            v-if="stocks"
            no-results-text="Нет результатов"
            no-data-text="Нет данных"
            :footer-props="{
                'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                'items-per-page-text': 'Записей на странице',
            }"
            :headers="headers"
            :search="search"
            :items="stocks">
            <template v-slot:item.image="{item}">
                <img
                    :src="'../storage/' + item.image"
                    style="max-width: 200px; height: auto;"
                    alt="">
            </template>
            <template v-slot:item.type="{item}">
                <span>{{item.service_id === null ? `Акция` : `Баннер: ${item.service}`}}</span>
            </template>
            <template v-slot:item.status="{item}">
                <h4 class="green--text" v-if="item.is_visible">Показывается</h4>
                <h4 class="red--text" v-else>Скрыто</h4>
            </template>
            <template v-slot:item.action="{item}">
                <v-btn icon @click="showHideModal(item)">
                    <v-icon v-if="!item.is_visible">mdi-eye</v-icon>
                    <v-icon v-else>mdi-eye-off</v-icon>
                </v-btn>
                <v-btn icon @click="showEditModal(item)">
                    <v-icon>mdi-pencil-outline</v-icon>
                </v-btn>
                <v-btn icon @click="showDeleteModal(item)">
                    <v-icon>mdi-delete</v-icon>
                </v-btn>
            </template>
            <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
            </template>
        </v-data-table>
        <ConfirmationModal
            message="Вы действительно хотите удалить выбранную акцию?"
            :state="deleteModal"
            ok-message="Удалить"
            v-on:cancel="closeDeleteModal"
            v-if="isAdmin"
            v-on:confirm="deleteStock"/>
        <ConfirmationModal
            v-if="isAdmin"
            ok-message="Скрыть"
            :message="modalMessage"
            :state="hideModal"
            v-on:cancel="closeHideModal"
            v-on:confirm="changeStockStatus"/>
        <AddNewsModal
            v-if="isAdmin"
            title="Добавление баннера"
            v-on:onClose="closeAddModal"
            v-on:onSave="stockCreated"
            :state="addModal"/>
        <AddNewsModal
            v-if="isAdmin"
            title="Редактирование баннера"
            v-on:onClose="closeEditModal"
            v-on:onSave="stockEdited"
            :service-id="editId"
            ok-button="Сохранить"
            :edit-mode="true"
            :state="editModal"/>
    </div>
</template>

<script>
    import showToast from "../../../utils/Toast";
    import AddNewsModal from "../../Modals/AddNewsModal/AddNewsModal";
    import ConfirmationModal from "../../Modals/ConfirmationModal/ConfirmationModal";
    import ACTIONS from "../../../store/actions";

    export default {
        components: {
            ConfirmationModal, AddNewsModal
        },
        props: {
            isAdmin: {
                type: Boolean
            }
        },
        computed: {
            stocks() {
                return this.$store.getters.stocks;
            },
            headers() {
                const headers = [
                    {
                        text: 'Наименование',
                        value: 'title',
                        sortable: false,
                    },
                    {
                        text: 'Дата создания',
                        value: 'date',
                        sortable: false
                    },
                    {
                        text: 'Тип',
                        value: 'type',
                        sortable: false
                    },
                    {
                        text: 'Статус',
                        value: 'status',
                        sortable: false
                    },
                    {
                        text: 'Баннер',
                        value: 'image',
                        sortable: false,
                        align: 'center'
                    }

                ];

                if (this.isAdmin) {
                    headers.push({
                        text: 'Действие',
                        value: 'action',
                        sortable: false
                    })
                }

                return headers;
            }
        },
        data: () => ({
            modalKey: 0,
            currentStock: null,
            modalMessage: '',
            deleteModal: false,
            editId: null,
            hideModal: false,
            addModal: false,
            editModal: false,
            search: '',
        }),
        methods: {
            showDeleteModal(e) {
                this.currentStock = e;
                this.deleteModal = true;
            },
            showHideModal(e) {
                this.currentStock = e;
                if (this.currentStock.is_visible) {
                    this.modalMessage = 'Вы действительно хотите скрыть данную акцию?';
                } else {
                    this.modalMessage = 'Вы действительно хотите показать данную акцию?';
                }
                this.hideModal = true;
            },
            showEditModal(e) {
                this.editId = e.id;
                this.editModal = true;
            },
            closeDeleteModal() {
                this.deleteModal = false;
            },
            closeHideModal() {
                this.hideModal = false;
            },
            closeAddModal() {
                this.addModal = false;
                this.modalKey++;
            },
            closeEditModal() {
                this.editModal = false;
                this.editId = null;
            },
            stockCreated() {
                this.addModal = false;
                showToast('Акция успешно добавлена!');
                this.modalKey++;
                this.currentStock = null;
            },
            stockEdited() {
                this.editModal = false;
                showToast('Акция успешно обновлена!');
                this.editId = null;
            },
            async deleteStock() {
                await this.$store.dispatch(ACTIONS.DELETE_STOCK, this.currentStock.id);
                this.currentStock = null;
                this.deleteModal = false;
            },
            async changeStockStatus() {
                this.hideModal = false;
                await this.$store.dispatch(ACTIONS.CHANGE_STOCK_STATUS, {
                    id: this.currentStock.id,
                    is_visible: !this.currentStock.is_visible
                });
                showToast('Акция успешно обновлена!');
                this.currentStock = null;
            }
        },
    }
</script>

<style scoped>

</style>
