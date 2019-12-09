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
            :items="allItems"
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
                <v-btn icon @click="showDeleteModal = true">
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
            v-on:cancel="showDeleteModal = false"
            v-on:confirm="showDeleteModal = false"/>
    </div>
</template>

<script>
    import ConfirmationModal from "../../Modals/ConfirmationModal/ConfirmationModal";
    import getRandomArbitrary from "../../../utils/Random";

    export default {
        components: {ConfirmationModal},
        data: () => ({
            showDeleteModal: false,
            search: '',
            items: [
                {
                    title: 'Внимание!',
                    body: 'Вам необходимо закрыть задолженность до конца текущего месяца, иначе действие нашей охраны будут приостановлены!',
                    date: '22.10.2019',
                    recipient_count: 195,
                    action: null,
                },
                {
                    title: 'Внимание!',
                    body: 'Вам необходимо закрыть задолженность до конца текущего месяца, иначе действие нашей охраны будут приостановлены!',
                    date: '22.10.2019',
                    recipient_count: 195,
                    action: null,
                },
                {
                    title: 'Внимание!',
                    body: 'Вам необходимо закрыть задолженность до конца текущего месяца, иначе действие нашей охраны будут приостановлены!',
                    date: '22.10.2019',
                    recipient_count: 195,
                    action: null,
                },
                {
                    title: 'Внимание!',
                    body: 'Вам необходимо закрыть задолженность до конца текущего месяца, иначе действие нашей охраны будут приостановлены!',
                    date: '22.10.2019',
                    recipient_count: 195,
                    action: null,
                },
                {
                    title: 'Внимание!',
                    body: 'Вам необходимо закрыть задолженность до конца текущего месяца, иначе действие нашей охраны будут приостановлены!',
                    date: '22.10.2019',
                    recipient_count: 195,
                    action: null,
                },
                {
                    title: 'Внимание!',
                    body: 'Вам необходимо закрыть задолженность до конца текущего месяца, иначе действие нашей охраны будут приостановлены!',
                    date: '22.10.2019',
                    recipient_count: 195,
                    action: null,
                },
                {
                    title: 'Внимание!',
                    body: 'Вам необходимо закрыть задолженность до конца текущего месяца, иначе действие нашей охраны будут приостановлены!',
                    date: '22.10.2019',
                    recipient_count: 195,
                    action: null,
                },
                {
                    title: 'Внимание!',
                    body: 'Вам необходимо закрыть задолженность до конца текущего месяца, иначе действие нашей охраны будут приостановлены!',
                    date: '22.10.2019',
                    recipient_count: 195,
                    action: null,
                },
                {
                    title: 'Внимание!',
                    body: 'Вам необходимо закрыть задолженность до конца текущего месяца, иначе действие нашей охраны будут приостановлены!',
                    date: '22.10.2019',
                    recipient_count: 195,
                    action: null,
                },
                {
                    title: 'Внимание!',
                    body: 'Вам необходимо закрыть задолженность до конца текущего месяца, иначе действие нашей охраны будут приостановлены!',
                    date: '22.10.2019',
                    recipient_count: 195,
                    action: null,
                },
            ],
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
                    value: 'author',
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
            allItems() {
                return this.items.map(item => {
                   const num = getRandomArbitrary(1, 100);
                   item.author = num > 50 ? 'Автоматическая рассылка' : 'Администратор';
                   return item;
                });
            }
        }
    }
</script>

<style scoped>

</style>
