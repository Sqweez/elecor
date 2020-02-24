<template>
    <div class="mt-2">
        <v-text-field
            label="Поиск"
            clearable
            single-line
            v-model="search"/>
        <v-data-table
            no-data-text="Нет данных"
            no-results-text="Нет результатов"
            :items="items"
            :footer-props="{
                'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                'items-per-page-text': 'Записей на странице',
            }"
            :headers="headers"
            :search="search"
        >
            <template v-slot:item.body="{item}">
                <i>{{ item.body }}</i>
            </template>
            <template v-slot:item.action="{item}">
                <v-btn icon @click="id = item.id; showDeleteModal = true">
                    <v-icon>mdi-delete</v-icon>
                </v-btn>
            </template>
            <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
            </template>
        </v-data-table>
        <ConfirmationModal
            :state="showDeleteModal"
            message="'Вы действительно хотите удалить данную рассылку из истории?"
            v-on:cancel="id = null; showDeleteModal = false"
            v-on:confirm="deleteHistory"/>
    </div>
</template>

<script>
    import ConfirmationModal from "../../Modals/ConfirmationModal/ConfirmationModal";
    import getRandomArbitrary from "../../../utils/Random";
    import {deleteHistory, getMailingHistory} from "../../../api/mailing";
    import showToast from "../../../utils/Toast";

    export default {
        components: {ConfirmationModal},
        async mounted() {
            this.items = await getMailingHistory();
        },
        data: () => ({
            showDeleteModal: false,
            search: '',
            items: [],
            id: null,
            headers: [
                {
                    text: 'Заголовок',
                    value: 'title',
                    sortable: false
                },
                {
                    text: 'Текст',
                    value: 'body',
                    sortable: false
                },
                {
                    text: 'Дата рассылки',
                    value: 'date'
                },
                {
                    text: 'Количество получателей',
                    value: 'recipient_count',
                },
                {
                    text: 'Автор',
                    value: 'user',
                    sortable: false,
                },
                {
                    text: 'Действие',
                    value: 'action',
                    sortable: false,
                }
            ]
        }),
        computed: {
        },
        methods: {
            async deleteHistory() {
                await deleteHistory(this.id);
                this.items = this.items.filter(i => i.id !== this.id);
                this.id = null;
                this.showDeleteModal = false;
                showToast('Рассылка успешно удалена!');
            }
        }
    }
</script>

<style scoped>

</style>
