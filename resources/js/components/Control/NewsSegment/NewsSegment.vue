<template>
    <div>
        <div class="d-flex justify-content-between">
            <v-btn color="primary" class="button-add">
                Экспорт данных
                <v-icon>mdi-file-excel-box</v-icon>
            </v-btn>
            <v-btn color="primary" @click="addModal = true">
                Добавить новость
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
            no-results-text="Нет результатов"
            no-data-text="Нет данных"
            :footer-props="{
                'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                'items-per-page-text': 'Записей на странице',
            }"
            :headers="headers"
            :search="search"
            :items="items">
            <template v-slot:item.action="{item}">
                <v-btn icon @click="hideModal = true">
                    <v-icon v-if="!item.isVisible">mdi-eye</v-icon>
                    <v-icon v-else>mdi-eye-off</v-icon>
                </v-btn>
                <v-btn icon @click="editModal = true">
                    <v-icon>mdi-pencil-outline</v-icon>
                </v-btn>
                <v-btn icon @click="deleteModal = true">
                    <v-icon>mdi-delete</v-icon>
                </v-btn>
            </template>
            <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
            </template>
        </v-data-table>
        <ConfirmationModal
            message="Вы действительно хотите удалить выбранную новость?"
            :state="deleteModal"
            ok-message="Удалить"
            v-on:cancel="closeDeleteModal"
            v-on:confirm="closeDeleteModal"/>
        <ConfirmationModal
            ok-message="Скрыть"
            message="Вы действительно хотите скрыть выбранную новость?"
            :state="hideModal"
            v-on:cancel="closeHideModal"
            v-on:confirm="closeHideModal"/>
        <AddNewsModal
            v-on:onClose="closeAddModal"
            :state="addModal"/>
        <AddNewsModal
            title="Редактирование новости"
            v-on:onClose="closeEditModal"
            ok-button="Сохранить"
            :edit-mode="true"
            :state="editModal"/>
    </div>
</template>

<script>
    import ConfirmationModal from "../../Modals/ConfirmationModal/ConfirmationModal";
    import AddNewsModal from "../../Modals/AddNewsModal/AddNewsModal";
    import showToast from "../../../utils/Toast";
    export default {
        components: {
            AddNewsModal,
            ConfirmationModal
        },
        methods: {
            closeDeleteModal() {
                this.deleteModal = false;
            },
            closeHideModal() {
                this.hideModal = false;
            },
            closeAddModal() {
                this.addModal = false;
                showToast('', 'Новость успешно добавлена!')
            },
            closeEditModal() {
                this.editModal = false;
                showToast('', 'Новость успешно изменена!')
            }
        },
        data: () => ({
            editModal: false,
            addModal: false,
            hideModal: false,
            deleteModal: false,
            headers: [
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
                    text: 'Действие',
                    value: 'action',
                    sortable: false
                }
            ],
            items: [
                {
                    title: 'Новая новость',
                    date: '21.10.2019',
                    isVisible: true,
                },
                {
                    title: 'Новая новость 2',
                    date: '21.10.2019',
                    isVisible: false,
                },
            ],
            search: ''
        })
    }
</script>

<style scoped>

</style>
