<template>
    <v-dialog max-width="800" v-model="state" persistent>
        <v-card>
            <v-card-title class="headline blue darken-1 justify-content-between">
                <span class="white--text">История баланса</span>
                <v-btn icon text class="float-right">
                    <v-icon color="white" @click="$emit('closeModal')">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text class="pt-2">
                <div v-if="history">
                    <v-flex>
                        <v-btn depressed :text="!paymentsSegment" :color="paymentsSegment ? 'primary' : 'default'"
                               @click="showPayments">
                            Начисления
                        </v-btn>
                        <v-btn depressed :text="!historySegment" :color="history ? 'primary' : 'default'"
                               @click="showHistory">
                            История
                        </v-btn>
                    </v-flex>
                    <div class="d-flex justify-content-between p-2">
                        <h3><b>Лицевой счет: </b> {{ this.history.personal_account | account_pipe }} </h3>
                        <div>
                            <h4>Фильтр</h4>
                            <v-select
                                v-model="filter"
                                style="max-width: 250px"
                                :items="dateFilters"
                                item-text="text"
                                item-value="value"
                            />
                        </div>
                    </div>
                    <div class="payments-block p-2" v-if="paymentsSegment">
                        <v-simple-table>
                            <template v-slot:default>
                                <thead>
                                <tr>
                                    <th>Начисление</th>
                                    <th>Оплата</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(payment) of payments" :key="payment.id">
                                    <td>{{ payment.month }}'{{payment.price}} <span v-if="payment.sale">({{ payment.sale }})</span>
                                    </td>
                                    <td>{{ payment.paid }}</td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </div>
                    <div class="history-block" v-if="historySegment">
                        <v-simple-table>
                            <template v-slot:default>
                                <thead>
                                <tr>
                                    <th>Дата</th>
                                    <th>Тип</th>
                                    <th>Кассир</th>
                                    <th>Сумма</th>
                                    <th>Основание</th>
                                    <th>Действие</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr v-for="(transaction) of transactions" :key="transaction.id">
                                    <td>{{ transaction.date }}</td>
                                    <td>
                                        <span v-if="transaction.balance < 0">Списание</span>
                                        <span v-else>Пополнение</span>
                                    </td>
                                    <td>{{ $store.getters.user_by_id(transaction.user_id).name || 'Неизвестно' }}</td>
                                    <td>{{ transaction.balance | positive }}</td>
                                    <td>
                                        <span v-if="transaction.sale">{{ transaction.sale }}</span>
                                        <span v-else>Абонентская плата</span>
                                    </td>
                                    <td>
                                        <v-btn icon @click="deleteModal = true; deleteId = transaction.id">
                                            <v-icon>
                                                mdi-delete
                                            </v-icon>
                                        </v-btn>
                                    </td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </div>
                </div>
                <div v-else style="min-height: 400px" class="d-flex justify-content-center align-items-center">
                    <v-progress-circular
                        indeterminate
                        size="65"
                        color="primary"
                    ></v-progress-circular>
                </div>
            </v-card-text>
        </v-card>
        <ConfirmationModal
            :state="deleteModal"
            v-on:cancel="deleteModal = false; deleteId = null"
            v-on:confirm="deleteTransaction"
            :message="'Вы действительно хотите удалить выбранную транзакцию?'" />
    </v-dialog>
</template>

<script>
    import {deleteTransaction, getHistory} from "../../../api/connection";
    import moment from 'moment';
    import {MONTHS} from "../../../config/consts";
    import ConfirmationModal from "../ConfirmationModal/ConfirmationModal";
    import showToast from "../../../utils/Toast";
    import ACTIONS from "../../../store/actions";
    export default {
        components: {ConfirmationModal},
        data: () => ({
            paymentsSegment: true,
            history: null,
            sum: 0,
            historySegment: false,
            totalSum: null,
            deleteModal: false,
            deleteId: null,
            filter: 0,
            dateFilters: [
                {
                    text: 'За последние полгода',
                    value: 0,
                },
                {
                    text: 'За последний год',
                    value: 1,
                },
                {
                    text: 'За все время',
                    value: 2,
                }
            ]
        }),
        computed: {
            payments() {
                let payments = this.history.payments;
                let date = null;
                if (this.filter === 0) {
                    date = moment().subtract(6, 'months');
                    console.log(date);
                    return payments.filter(p => {
                        let p_date = moment(p.created_at);
                        return moment(p_date).isAfter(moment(date)) || moment(p_date).isSame(moment(date), 'day');
                    });
                }
                if (this.filter === 1) {
                    date = moment().subtract(1, 'year');
                    console.log(date);
                    return payments.filter(p => {
                        let p_date = moment(p.created_at);
                        return moment(p_date).isAfter(moment(date)) || moment(p_date).isSame(moment(date), 'day');
                    });
                }
                return this.history.payments;
            },
            transactions() {
                let date = null;
                let transactions = this.history.transactions;
                if (this.filter === 0) {
                    date = moment().subtract(6, 'months');
                    console.log(date);
                    return transactions.filter(p => {
                        let p_date = moment(p.created_at);
                        return moment(p_date).isAfter(moment(date)) || moment(p_date).isSame(moment(date), 'day');
                    });
                }
                if (this.filter === 1) {
                    date = moment().subtract(1, 'year');
                    return transactions.filter(p => {
                        let p_date = moment(p.created_at);
                        return moment(p_date).isAfter(moment(date)) || moment(p_date).isSame(moment(date), 'day');
                    });
                }

                return this.history.transactions;
            }
        },
        filters: {
            positive(value) {
                return (value < 0) ? value * -1 : value;
            }
        },
        props: {
            state: {
                type: Boolean,
                default: true,
            },
            connection: {
                type: Number,
                default: 0,
            }
        },
        methods: {
            showPayments() {
                this.paymentsSegment = true;
                this.historySegment = false;
            },
            showHistory() {
                this.paymentsSegment = false;
                this.historySegment = true;
            },
            async deleteTransaction() {
                await deleteTransaction(this.deleteId);
                await this.getClient();
                await this.modalInit();
                showToast('Транзакция удалена');
                this.deleteModal = false;
                this.deleteId = null;
            },
            async getClient() {
                const id = this.$route.params.userId;
                await this.$store.dispatch(ACTIONS.GET_CLIENT, id)
            },
            async modalInit() {
                if (this.connection) {
                    this.history = await getHistory(this.connection);
                    this.sum = this.history.sum;
                    this.history.payments = this.history.payments.map(payment => {
                        if ((this.sum - payment.price) > 0) {
                            payment.paid = payment.price;
                        } else {
                            payment.paid = this.sum;
                        }
                        if (this.sum <= 0) {
                            payment.paid = 0;
                        }
                        this.sum -= payment.paid;
                        let month = moment(payment.created_at).format('MM');
                        payment.month = MONTHS[month - 1];
                        return payment;
                    }).reverse();
                    this.history.transactions = this.history.transaction.map(transaction => {
                        transaction.date = moment(transaction.created_at).format('DD.MM.YYYY');
                        return transaction;
                    }).reverse();
                }
            }
        },
        async mounted() {
            await this.modalInit();
        }
    }
</script>

<style scoped>

</style>
