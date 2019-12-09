<template>
    <div>
        <v-form class="p-3">
            <v-select label="Тип отправки" :items="sendingTypes"></v-select>
            <v-flex>
                <v-select
                    v-if="!isFreeTemplate"
                    label="Шаблон"
                    :items="templates"
                    item-text="title"
                    item-value="title"
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
                item-key="_id"
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
                <template v-slot:item.personalAccount="{ item }">
                    <ul>
                        <li v-for="(i, index) of item.personalAccount" :key="index">{{ i }}</li>
                    </ul>
                </template>
                <template v-slot:item.address="{ item }">
                    <ul>
                        <li v-for="(i, index) of item.address" :key="index">{{ i }}</li>
                    </ul>
                </template>
                <template v-slot:item.trademark="{ item }">
                    <ul>
                        <li v-for="(i, index) of item.trademark" :key="index">{{ i }}</li>
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
            <v-btn color="primary" @click="showModal = true">Отправить рассылку</v-btn>
        </v-form>
        <ConfirmationModal
            :state="showModal"
            v-on:cancel="showModal = false"
            v-on:confirm="sendMailing"
            message="Вы действительно хотите отправить эту рассылку?" />
    </div>
</template>

<script>
    import ConfirmationModal from "../../Modals/ConfirmationModal/ConfirmationModal";
    import showToast from "../../../utils/Toast";
    String.prototype.replaceAll = function(search, replacement) {
        let target = this;
        return target.split(search).join(replacement);
    };
    export default {
        components: {ConfirmationModal},
        methods: {
            addVariableToTemplate(variable) {
                this.body += variable.key;
            },
            sendMailing() {
                this.showModal = false;
                showToast('Успех', 'Рассылка была отправлена');
            }
        },
        data: () => ({
            showModal: false,
            sendingTypes: [
                'Все', 'SMS', 'Пуш-уведомления'
            ],
            variables: [
                {
                    key: '%ИМЯ%',
                    name: 'Имя клиента',
                    example: 'Александр Андреевич'
                },
                {
                    key: '%ДОЛГ%',
                    name: 'Задолженность',
                    example: '5000'
                },
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
                    value: 'fullName',
                },
                {text: 'Лицевой счет', value: 'personalAccount', sortable: false},
                {text: 'Адрес', value: 'address', sortable: false},
                {text: 'Торговое наименование', value: 'trademark', sortable: false}
            ],
            isFreeTemplate: false,
            clientGroup: null,
            clientGroups: [
                'Все', 'Задолжники', 'Именинники'
            ],
            title: '',
            body: '',
            templates: [
                {
                    title: 'День рождения',
                    body: 'Уважаемый, %ИМЯ%! Поздравляем вас с днем рождения!',
                    action: null,
                },
                {
                    title: 'Долг',
                    body: 'Уважаемый, %ИМЯ%! Поздравляем вас с днем рождения!',
                    action: null,
                },
                {
                    title: 'Новый год',
                    body: 'Уважаемый, %ИМЯ%! Поздравляем вас с днем рождения!',
                    action: null,
                }
            ],
        }),
        computed: {
            clients() {
                return this.$store.getters.allClients;
            },
            message() {
                let outputMessage = this.body;
                this.variables.forEach(v => {
                    outputMessage = outputMessage.replaceAll(v.key, v.example);
                });

                return outputMessage;
            }
        }
    }
</script>

<style scoped>

</style>
