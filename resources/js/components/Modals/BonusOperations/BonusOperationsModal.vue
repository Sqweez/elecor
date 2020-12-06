<template>
    <v-dialog persistent max-width="1300" v-model="state">
        <v-card>
            <v-card-title class="d-flex justify-content-between blue darken-1">
                <span class="white--text">Операции с бонусами</span>
                <v-btn icon @click="onClose" color="white">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <div class="my-5 segment-buttons-container">
                    <v-btn depressed :color="currentSegment === 'operations' ? 'primary' : ''"
                           @click="currentSegment = 'operations'">Операции
                    </v-btn>
                    <v-btn depressed :color="currentSegment === 'statement' ? 'primary' : ''"
                           @click="currentSegment = 'statement'">Выписка
                    </v-btn>
                </div>
                <div id="operations-segment" v-if="currentSegment === 'operations'">
                    <h3>
                        <span class="font-weight-bold">Текущие бонусы:</span>
                        <span>{{ client.bonuses | bonus}}</span>
                    </h3>
                    <p v-if="client.bonuses === 0" class="red--text">
                        Некоторые операции могут быть недоступны, т.к. сумма бонусов на счету недостаточна для их выполнения
                    </p>
                    <v-radio-group v-model="operationType" row>
                        <v-radio
                            v-for="(type, key) of referralOperationTypes"
                            :disabled="type.min_bonus === client.bonuses"
                            :key="`operation-type-${key}`"
                            :label="`${type.name}`"
                            :value="type"
                        ></v-radio>
                    </v-radio-group>
                    <v-text-field
                        label="Количество бонусов"
                        type="number"
                        v-model.number="amountValue"
                        @blur="amountValue = amount"
                    />
                    <v-select
                        v-if="operationType && operationType.id === 2"
                        label="Услуга"
                        :items="client.connections"
                        item-value="id"
                        :item-text="getSelectText"
                        v-model="connection_id"
                    />
                    <v-text-field type="text" label="Комментарий" v-model.trim="comment"/>
                </div>
                <div id="operations-statement" v-if="currentSegment === 'statement'">
                    <v-simple-table v-slot:default>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Сумма</th>
                            <th>Тип операции</th>
                            <th>Комментарий</th>
                            <th>Услуга</th>
                            <th>Пользователь</th>
                            <th>Дата</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(item, key) of client.bonus_transactions" :key="`bonus-transaction-${key}`">
                            <td>{{ key + 1 }}</td>
                            <td>{{ item.amount | money}}</td>
                            <td>{{ item.operation }}</td>
                            <td>{{ item.comment }}</td>
                            <td>{{ item.connection ? `${item.connection.personal_account} | ${item.connection.trademark}` : '-'}}</td>
                            <td>{{ item.user }}</td>
                            <td>{{ item.date }}</td>
                        </tr>
                        </tbody>
                    </v-simple-table>
                </div>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions v-if="!loading">
                <v-btn text @click="onClose" :disabled="IS_LOADING">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text @click="createOperation" color="success" v-if="currentSegment === 'operations'" :disabled="IS_LOADING">
                    Подтвердить
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
            <v-progress-linear
                indeterminate
                :active="IS_LOADING"
                color="green"
                absolute
                bottom
            ></v-progress-linear>
        </v-card>
    </v-dialog>
</template>

<script>
    import GETTERS from "../../../store/getters";
    import showToast from "../../../utils/Toast";
    import {TOAST_TYPE} from "../../../config/consts";

    export default {
        watch: {
            amountValue(value) {
                const _value = this.operationType.max_amount ? Math.min(value, this.client.bonuses) : value;
                this.amount = Math.max(1, _value);
            },
            operationType(value) {
                const _value = value.max_amount ? Math.min(this.amount, this.client.bonuses) : this.amount;
                this.amount = Math.max(1, _value);
                this.amountValue = this.amount;

                console.log(value);
                if (value.id === 2) {
                    console.log(123);
                    this.connection_id = this.client.connections[0].id;
                } else {
                    this.connection_id = -1;
                }
            }
        },
        methods: {
            onClose() {
                this.resetFields();
                this.$emit('close');
            },
            getSelectText(e) {
                return `${e.personal_account} | ${e.trademark} | ${e.address} | ${e.service_name}`;
            },
            resetFields() {
                this.operationType = this.referralOperationTypes[0];
                this.amount = 1;
                this.comment = '';
                this.connection_id = -1;
            },
            async createOperation() {

                if (!this.validateOperation()) {
                    return false;
                }

                const operation = {
                    operation_type: this.operationType.id,
                    amount: this.amount,
                    comment: this.comment,
                    user_id: this.user.id,
                    client_id: this.client.id,
                    connection_id: this.connection_id
                };

                await this.$store.dispatch('createReferralOperation', operation)

                this.resetFields();
            },
            validateOperation() {
                if (!this.comment.length) {
                    showToast('Введите комментарий, чтобы создать операцию', 'Ошибка', TOAST_TYPE.ERROR)
                    return false;
                }
                if (this.amount <= 0) {
                    showToast('Количество введенных бонусов должно быть больше нуля', 'Ошибка', TOAST_TYPE.ERROR)
                    return false;
                }

                if (this.operationType.max_amount === 'current_balance' && this.amount > this.client.bonuses) {
                    showToast('Количество введенных бонусов не должно превышать текущий остаток', 'Ошибка', TOAST_TYPE.ERROR)
                    return false;
                }

                return true;
            }
        },
        computed: {
            client() {
                return this.$store.getters[GETTERS.CLIENT];
            },
            referralOperationTypes() {
                return this.$store.getters.REFERRAL_OPERATION_TYPES;
            },
            user() {
                return this.$store.getters.user;
            },
            IS_LOADING() {
                return this.$store.getters.IS_LOADING;
            }
        },
        data: () => ({
            loading: false,
            currentSegment: 'operations',
            operationType: null,
            amount: 1,
            amountValue: 1,
            comment: '',
            connection_id: -1,
        }),
        props: {
            state: {
                type: Boolean,
                default: false,
            }
        },
        async created() {
            await this.$store.dispatch('getReferralOperationTypes');
            this.operationType = this.referralOperationTypes[0];
        }
    }
</script>

<style scoped>
    .segment-buttons-container {
        display: flex;
        column-gap: 15px;
    }

    .segment-buttons-container > button {
        flex: 1 0 auto;
    }
</style>
