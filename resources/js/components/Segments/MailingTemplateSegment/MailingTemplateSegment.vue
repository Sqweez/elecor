<template>
    <div class="mt-3">
        <div class="d-flex justify-content-end">
            <v-btn color="primary" class="mb-2" @click="showAddModal = true">Добавить шаблон</v-btn>
        </div>
        <v-data-table
            no-results-text="Нет результатов"
            no-data-text="Нет данных"
            :footer-props="{
                'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                'items-per-page-text': 'Записей на странице',
            }"
            :headers="headers"
            :items="items"
            :search="search">
            <template v-slot:item.action="{item}">
                <v-btn icon @click="showEditModal = true">
                    <v-icon>mdi-pencil</v-icon>
                </v-btn>
                <v-btn icon>
                    <v-icon>mdi-delete</v-icon>
                </v-btn>
            </template>
            <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
            </template>
        </v-data-table>
        <AddMailingTemplateModal
            :state="showAddModal"
            v-on:onClose="showAddModal = false"
            v-on:onSave="onSave"/>
        <AddMailingTemplateModal
            title="Редактирование шаблона"
            :state="showEditModal"
            :edit-mode="true"
            v-on:onClose="showEditModal = false"
            v-on:onSave="onEdit"/>
        <ConfirmationModal
            :state="showDeleteModal"
            v-on:confirm="showDeleteModal = false"
            v-on:cancel="showDeleteModal = false"
            message="Вы действительно хотите удалить выбранный шаблон?"
        />
    </div>
</template>

<script>
    import AddMailingTemplateModal from "../../Modals/AddMailingTemplateModal/AddMailingTemplateModal";
    import showToast from "../../../utils/Toast";
    import ConfirmationModal from "../../Modals/ConfirmationModal/ConfirmationModal";

    export default {
        components: {ConfirmationModal, AddMailingTemplateModal},
        methods: {
            onEdit() {
                this.showEditModal = false;
                showToast('', 'Шаблон успешно изменен!')
            },
            onSave() {
                this.showAddModal = false;
                showToast('', 'Шаблон успешно добавлен!');
            }
        },
        data: () => ({
            showEditModal: false,
            showAddModal: false,
            items: [
                {
                    title: 'День рождения',
                    body: 'Уважаемый, %ИМЯ%! Поздравляем вас с днем рождения!',
                    action: null,
                },
                {
                    title: 'День рождения',
                    body: 'Уважаемый, %ИМЯ%! Поздравляем вас с днем рождения!',
                    action: null,
                },
                {
                    title: 'День рождения',
                    body: 'Уважаемый, %ИМЯ%! Поздравляем вас с днем рождения!',
                    action: null,
                }
            ],
            headers: [
                {
                    text: 'Наименование шаблона',
                    value: 'title',
                    sortable: false,
                },
                {
                    text: 'Текст шаблона',
                    value: 'body',
                    sortable: false
                },
                {
                    text: 'Действие',
                    value: 'action',
                    sortable: false,
                }
            ],
            search: '',
        }),
    }
</script>

<style scoped>

</style>
