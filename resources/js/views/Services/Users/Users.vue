<template>
    <div>
        <v-card>
            <v-card-title>
                Пользователи
            </v-card-title>
            <v-card-text>
                <div class="p-3">
                    <div class="d-flex justify-content-end mb-3">
                        <v-btn color="primary" @click="addModal = true">
                            Добавить пользователя
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </div>
                    <v-data-table
                        :headers="headers"
                        no-results-text="Нет результатов"
                        no-data-text="Нет данных"
                        :items="items"
                        :footer-props="{
                            'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                            'items-per-page-text': 'Записей на странице',
                        }">
                        <template v-slot:item.action="{item}">
                            <v-btn icon @click="editModal = true">
                                <v-icon>mdi-pencil</v-icon>
                            </v-btn>
                            <v-btn icon @click="deleteModal = true">
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
        <AddUserModal
            :state="addModal"
            v-on:onClose="addModal = false"
            v-on:onSave="onSave"/>
        <AddUserModal
            :state="editModal"
            :edit-mode="true"
            title="Редактирование пользователя"
            v-on:onClose="editModal = false"
            v-on:onSave="onEdit"/>
        <ConfirmationModal
            :state="deleteModal"
            v-on:confirm="deleteModal = false"
            v-on:cancel="deleteModal = false"
            message="Вы действительно хотите удалить выбранного пользователя?" />
    </div>
</template>

<script>
    import ConfirmationModal from "../../../components/Modals/ConfirmationModal/ConfirmationModal";
    import AddUserModal from "../../../components/Modals/AddUserModal/AddUserModal";
    import showToast from "../../../utils/Toast";
    export default {
        components: {ConfirmationModal, AddUserModal},
        methods: {
            onSave() {
                this.addModal = false;
                showToast('', 'Пользователь  успешно добавлен!');
            },
            onEdit() {
                this.editModal = false;
                showToast('', 'Пользователь успешно изменен!');
            }
        },
        data: () => ({
            editModal: false,
            deleteModal: false,
            addModal: false,
            items: [
                {
                    login: 'elecor',
                    displayName: 'Администратор',
                    password: '123456',
                    role: 'Суперпользователь'
                },
                {
                    login: 'elecor123',
                    displayName: 'Кассир',
                    password: '123456',
                    role: 'Кассир'
                },
            ],
            headers: [
                {
                    text: 'Имя',
                    value: 'displayName',
                    sortable: false
                },
                {
                    text: 'Логин',
                    value: 'login',
                    sortable: false
                },
                {
                    text: 'Пароль',
                    value: 'password',
                    sortable: false
                },
                {
                    text: 'Роль',
                    value: 'role',
                    sortable: false,
                },
                {
                    text: 'Действие',
                    value: 'action',
                    sortable: false,
                }
            ]
        })
    }
</script>

<style scoped>

</style>
