<template>
    <v-dialog max-width="800" persistent v-model="state">
        <v-card>
            <v-card-title class="headline blue darken-1 justify-content-between">
                <span class="white--text">Подключение новой услуги</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('cancel')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text class="modal-text">
                <v-form v-model="valid" v-if="!loading">
                    <v-select
                        label="Услуга"
                        :rules="serviceRules"
                        :items="services"
                        item-text="name"
                        item-value="id"
                        @change="setData"
                        v-model="connection.service_id"
                    />
                    <v-text-field
                        v-model="connection.trademark"
                        label="Торговое наименование"/>
                    <v-text-field
                        label="Адрес"
                        v-model="connection.address"/>
                    <v-text-field
                        label="Лицевой счет"
                        v-mask="'## ## ##'"
                        :rules="personalAccountRules"
                        @blur="checkDuplicates"
                        :error-messages="errorMessage"
                        v-model="connection.personal_account"/>
                    <v-text-field
                        type="number"
                        prefix="₸"
                        label="Тариф"
                        v-model="connection.price"/>
                    <v-dialog
                        ref="dialog"
                        v-model="calendarModal"
                        :return-value.sync="connection.date_start"
                        persistent
                        width="290px"
                    >
                        <template v-slot:activator="{ on }">
                            <v-text-field
                                v-model="correctDate"
                                label="Дата начала"
                                readonly
                                v-on="on"
                            ></v-text-field>
                        </template>
                        <v-date-picker v-model="connection.date_start" locale="ru" first-day-of-week="1" scrollable>
                            <v-spacer></v-spacer>
                            <v-btn text color="primary" @click="calendarModal = false">Отмена</v-btn>
                            <v-btn text color="primary" @click="$refs.dialog.save(connection.date_start)">OK</v-btn>
                        </v-date-picker>
                    </v-dialog>
                    <v-text-field
                        prefix="₸"
                        type="number"
                        label="Стоимость на месяц"
                        v-model.number="connection.month_price"></v-text-field>
                </v-form>
                <v-divider></v-divider>
            </v-card-text>
            <v-progress-linear
                indeterminate
                :active="loading"
                color="green"
                absolute
                bottom
            ></v-progress-linear>
            <v-card-actions v-if="!loading">
                <v-spacer></v-spacer>
                <v-btn text @click="$emit('cancel')">Отмена</v-btn>
                <v-btn text color="success" @click="connectService" :disabled="!valid">Подключить</v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import moment from 'moment';
    import GETTERS from "../../../store/getters";
    import {getDuplicate} from "../../../api/connection";
    import ACTIONS from "../../../store/actions";
    import showToast from "../../../utils/Toast";
    import {sendPushToClient} from "../../../api/client/clientApi";
    String.prototype.replaceAll = function (search, replacement) {
        let target = this;
        return target.split(search).join(replacement);
    };
    export default {
        data: () => ({
            valid: true,
            calendarModal: false,
            picker: true,
            isUnique: true,
            loading: false,
            errorMessage: '',
            connection: {
                service_id: null,
                address: '',
                personal_account: null,
                price: 0,
                date_start: moment().format('YYYY-MM-DD'),
                month_price: 0,
                trademark: '',
                client_id: null,
            },
            personalAccountRules: [
                v => !!v || 'Введите лицевой счет',
            ],
            serviceRules: [
                v => !!v || 'Выберите услугу!'
            ]
        }),
        props: {
            state: {
                type: Boolean,
                default: false,
            }
        },
        methods: {
            setData() {
                this.setTrademark();
                this.setPrice();
            },
            async connectService() {
                this.loading = true;
                this.connection.client_id = this.$route.params.userId;
                this.connection.personal_account = this.connection.personal_account.replaceAll(' ', '');
                this.connection.balance = this.connection.month_price * -1;
                await this.$store.dispatch(ACTIONS.ADD_CONNECTION, this.connection);
                const service_name = this.services.filter(s => s.id === this.connection.service_id)[0].name;
                const message = {
                    title: 'Внимание',
                    body: `Вы были подключены к услуге "${service_name}"`
                };
                await this.sendMessage(message);
                if (this.connection.month_price > 0) {
                    message.body = `С Вашего баланса произошло списание ${this.connection.month_price} тг по услуге "${service_name}!"`;
                    await this.sendMessage(message);
                }
                this.$emit('connect');
                this.loading = false;
            },
            async sendMessage(message) {
                const _message = {
                    ...message,
                    client_id: this.connection.client_id,
                    user_id: 1,
                };
                const response = await sendPushToClient(_message);
                console.log(response);
                this.showSendModal = false;
            },
            async checkDuplicates() {
                if (!this.connection.personal_account) {
                    return;
                }
                const personalAccount = this.connection.personal_account.replaceAll(' ', '');
                let data = await getDuplicate(personalAccount);
                if (data === 1) {
                    this.errorMessage = 'Данный лицевой счет уже используется!';
                } else {
                    this.errorMessage = '';
                }
            },
            setTrademark() {
                let trademark = '';
                if (this.connection.service_id) {
                    const {trademark_default} = this.services.find(c => c.id === this.connection.service_id);
                    trademark = trademark_default;
                }

                this.$set(this.connection, 'trademark', trademark);
            },

            setPrice() {
                let _price = '';
                if (this.connection.service_id) {
                    const {price} = this.services.find(c => c.id === this.connection.service_id);
                    _price = price;
                }

                this.$set(this.connection, 'price', _price);
            }
        },
        computed: {
            services() {
                return this.$store.getters[GETTERS.NORMAL_SERVICES];
            },
            correctDate() {
                let date = this.connection.date_start;
                if (!date) {
                    return null;
                }
                date = date.toString();
                date = date.split('-').reverse().join('.');
                return date;
            }
        },

    }
</script>

<style scoped>
    .modal-text {
        padding: 30px !important;
    }
</style>
