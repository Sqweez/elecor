<template>
    <div>
        <div
            v-if="$store.getters.debts.length === 0"
            class="text-center d-flex align-items-center justify-content-center"
            style="min-height: 651px">
            <v-progress-circular
                indeterminate
                size="65"
                color="primary"
            ></v-progress-circular>
        </div>
        <v-form class="p-3" v-else>
            <v-select
                v-model="sendType"
                item-value="id"
                item-text="name"
                label="Тип отправки"
                :items="sendingTypes"/>
            <v-flex>
                <v-select
                    v-if="!isFreeTemplate"
                    label="Шаблон"
                    :items="templates"
                    item-text="name"
                    item-value="id"
                    v-model="currentTemplate"
                />
                <v-checkbox
                    label="Произвольный шаблон"
                    v-model="isFreeTemplate"
                />
            </v-flex>
            <v-text-field label="Заголовок" v-model="title"/>
            <v-textarea label="Текст" auto-grow rows="5" counter v-model="body"/>
            <v-flex class="mb-2">
                <h4 class="ml-1">Переменные:</h4>
                <v-btn
                    @click="addVariableToTemplate(item)"
                    small
                    v-for="(item, index) of variables"
                    :key="index"
                    color="primary"
                    class="m-1">
                    {{ item.name }}
                </v-btn>
            </v-flex>
            <v-select label="Группа клиентов" :items="clientGroups" v-model="clientGroup"></v-select>
            <v-data-table
                item-key="id"
                show-select
                v-if="clientGroup"
                no-results-text="Нет результатов"
                no-data-text="Нет данных"
                :footer-props="{
                'items-per-page-options': [10, 15, {text: 'Все', value: -1}],
                'items-per-page-text': 'Записей на странице',
            }"
                :headers="headers"
                :items="clients"
                :search="search"
                v-model="selected"
            >
                <template v-slot:item.personal_accounts="{ item }">
                    <ul>
                        <li v-for="(i, index) of item.personal_accounts" :key="index">{{ i }}</li>
                    </ul>
                </template>
                <template v-slot:item.addresses="{ item }">
                    <ul>
                        <li v-for="(i, index) of item.addresses" :key="index">{{ i }}</li>
                    </ul>
                </template>
                <template v-slot:item.trademarks="{ item }">
                    <ul>
                        <li v-for="(i, index) of item.trademarks" :key="index">{{ i }}</li>
                    </ul>
                </template>
                <template slot="footer.page-text" slot-scope="{pageStart, pageStop, itemsLength}">
                    {{ pageStart }}-{{ pageStop }} из {{ itemsLength }}
                </template>
            </v-data-table>
            <div class="d-flex" v-if="clientGroup">
                <h4>Количество получателей: {{ selected.length }}</h4>
            </div>
            <v-divider></v-divider>
            <v-flex class="mt-2">
                <h3>Предпросмотр шаблона:</h3>
                <h4>{{ this.title }}</h4>
                <p>{{ this.message }}</p>
            </v-flex>
            <v-btn color="primary" @click="showModal = true" v-if="selected.length">Отправить рассылку</v-btn>
        </v-form>
        <ConfirmationModal
            :state="showModal"
            v-on:cancel="showModal = false"
            v-on:confirm="sendMailing"
            message="Вы действительно хотите отправить эту рассылку?"/>
    </div>
</template>

<script>
    import ConfirmationModal from "../../Modals/ConfirmationModal/ConfirmationModal";
    import showToast from "../../../utils/Toast";
    import {sendMailing} from "../../../api/mailing";

    String.prototype.replaceAll = function (search, replacement) {
        let target = this;
        return target.split(search).join(replacement);
    };
    export default {
        components: {ConfirmationModal},
        methods: {
            addVariableToTemplate(variable) {
                this.body += variable.key;
            },
            async sendMailing() {
                const mailing = {
                    title: this.title,
                    body: this.body,
                    clients: this.selected.map(s => s.id),
                    sendType: this.sendType
                };
                this.showModal = false;
                await sendMailing(mailing);
                showToast('Рассылка была отправлена');
            }
        },
        data: () => ({
            showModal: false,
            sendingTypes: [
                {id: 0, name: 'Все'}, {id: 1, name: 'SMS'}, {id: '2', name: 'Пуш-уведомления'}
            ],
            sendType: 2,
            variables: [
                {
                    key: '%ИМЯ%',
                    name: 'Имя клиента',
                    example: 'Александр Андреевич'
                },
              /*  {
                    key: '%ДОЛГ%',
                    name: 'Задолженность',
                    example: '5000'
                },*/
            ],
            methods: {
                addVariableToTemplate(variable) {
                    this.body += variable.key;
                }
            },
            search: '',
            selected: [],
            currentTemplate: null,
            headers: [
                {
                    text: 'Контрагент',
                    align: 'left',
                    sortable: false,
                    value: 'name',
                },
                {text: 'Лицевой счет', value: 'personal_accounts', sortable: false},
                {text: 'Адрес', value: 'addresses', sortable: false},
                {text: 'Торговое наименование', value: 'trademarks', sortable: false}
            ],
            isFreeTemplate: false,
            clientGroup: null,
            clientGroups: [
                'Все', 'Должники'
            ],
            title: '',
            body: '',
        }),
        watch: {
            currentTemplate() {
                const template = this.$store.getters.mailing_template(this.currentTemplate)
                this.body = template.body;
                this.title = template.title;
                return template;
            }
        },
        computed: {
            clients() {
                let clients = [];
                if (this.clientGroup === 'Все') {
                    clients = this.$store.getters.allClients;

                } else {
                    clients =  this.$store.getters.debts;
                }
                this.selected = [...clients];
                return clients;
            },
            message() {
                let outputMessage = this.body;
                this.variables.forEach(v => {
                    outputMessage = outputMessage.replaceAll(v.key, v.example);
                });

                return outputMessage;
            },
            templates() {
                return this.$store.getters.mailing_templates;
            },
        }
    }
</script>

<style scoped>

</style>
