<template>
    <div class="client-container">
        <v-card>
            <v-card-title>
                <span v-if="!client">Пожалуйста, подождите...</span>
                <span v-else>Информация о контрагенте</span>
            </v-card-title>
            <v-card-text>
                <div
                    class="text-center d-flex align-items-center justify-content-center"
                    style="min-height: 500px"
                    v-if="!client">
                    <v-progress-circular
                        indeterminate
                        size="65"
                        color="primary"
                    ></v-progress-circular>
                </div>
                <div v-else>
                    <client-bio
                        :client="client" :subjects="subjects"
                        v-on:editToggled="onEdit"
                        v-on:saveToggled="onSave"/>
                    <v-col cols="12">
                        <v-simple-table>
                            <template v-slot:default>
                                <thead>
                                <tr>
                                    <th class="text-left">Торговое наименование</th>
                                    <th class="text-center">Адрес</th>
                                    <th class="text-center">Тариф</th>
                                    <th class="text-center">Лицевой счет</th>
                                    <th class="text-center">Баланс</th>
                                    <th class="text-center">Действие</th>
                                </tr>
                                </thead>
                                <div class="d-flex-align-center justify-content-center p-3 w-100"
                                     v-if="!client.connections.length">
                                    <h2>Нет подключенных услуг</h2>
                                </div>
                                <tbody v-if="client.connections.length">
                                <tr v-for="item in client.connections" :key="item.id"
                                    v-if="!editConnectionMode || editingConnection.id === item.id"
                                    :class="{'deactivated': !item.is_active}">
                                    <td class="d-flex justify-content-between" style="padding-top: 5px">
                                        <v-text-field
                                            style="padding-top: 0;"
                                            v-model="editingConnection.trademark"
                                            v-if="editConnectionMode && editingConnection.id === item.id"
                                        />
                                        <span class="mt-2" v-if="!editConnectionMode">
                                            <span>{{ item.trademark }}</span>
                                        </span>
                                        <v-menu transition="slide-y-transition" class="menu">
                                            <template v-slot:activator="{ on }">
                                                <v-btn icon text class="float-left" v-on="on">
                                                    <v-icon>mdi-dots-vertical</v-icon>
                                                </v-btn>
                                            </template>
                                            <v-list>
                                                <v-list-item
                                                    @click="showDisconnectModal(item)"
                                                    link
                                                    v-if="item.is_active === 1"
                                                >
                                                    <v-list-item-title v-text="'Отключить'"></v-list-item-title>
                                                </v-list-item>
                                                <v-list-item
                                                    link
                                                    @click="showConnectModal(item)"
                                                    v-if="item.is_active === 0"
                                                >
                                                    <v-list-item-title v-text="'Подключить'"></v-list-item-title>
                                                </v-list-item>
                                                <v-list-item
                                                    @click="showDeleteModal(item)"
                                                    link
                                                    v-if="item.is_active === 0"
                                                >
                                                    <v-list-item-title v-text="'Удалить'"></v-list-item-title>
                                                </v-list-item>
                                                <v-list-item
                                                    link
                                                    @click="showHistoryModal(item)"
                                                >
                                                    <v-list-item-title v-text="'История'"></v-list-item-title>
                                                </v-list-item>
                                                <v-list-item
                                                    link
                                                    v-if="item.is_active === 1"
                                                    @click="showTempServiceModal(item)"
                                                >
                                                    <v-list-item-title v-text="'Разовые услуги'"></v-list-item-title>
                                                </v-list-item>
                                            </v-list>
                                        </v-menu>
                                    </td>
                                    <td class="text-center">
                                        <v-text-field
                                            v-model="editingConnection.address"
                                            v-if="editConnectionMode && editingConnection.id === item.id"/>
                                        <span v-if="!editConnectionMode">{{ item.address }}</span>
                                    </td>
                                    <td class="text-center">
                                        <v-text-field
                                            v-model="editingConnection.price"
                                            v-if="editConnectionMode && editingConnection.id === item.id"/>
                                        <span v-if="!editConnectionMode">{{ item.price }}</span>
                                    </td>
                                    <td class="text-center">
                                        <v-text-field
                                            v-model="editingConnection.personal_account"
                                            v-mask="'## ## ##'"
                                            v-if="editConnectionMode && editingConnection.id === item.id"/>
                                        <span
                                            v-if="!editConnectionMode">{{ item.personal_account | account_pipe}}</span>
                                    </td>
                                    <td class="text-center">
                                        <span v-if="!item.paymentMode">{{ item.balance }}</span>
                                        <v-text-field v-else v-model="balance" type="number" v-mask="'########'"/>
                                    </td>
                                    <td class="text-center d-flex align-items-center justify-content-center">
                                        <div v-if="!editConnectionMode">
                                            <v-btn color="primary"
                                                   v-if="!item.paymentMode"
                                                   :disabled="!item.is_active"
                                                   @click="triggerBalanceField(item)">
                                                Оплатить услугу
                                            </v-btn>
                                            <div v-else>
                                                <v-btn icon @click="cancelBalance(item)">
                                                    <v-icon>mdi-close</v-icon>
                                                </v-btn>
                                                <v-btn color="primary"
                                                       :disabled="!item.is_active"
                                                       @click="addBalance(item)">
                                                    Оплатить услугу
                                                </v-btn>
                                            </div>
                                        </div>
                                        <v-btn icon class="ml-1" @click="onEditClick(item)" :disabled="!item.is_active">
                                            <v-icon v-if="!editConnectionMode">mdi-pencil</v-icon>
                                            <v-icon v-else>mdi-check</v-icon>
                                        </v-btn>
                                    </td>
                                </tr>
                                </tbody>
                            </template>
                        </v-simple-table>
                    </v-col>
                </div>
            </v-card-text>
            <ConfirmationModal
                v-on:cancel="connectModal = false"
                v-on:confirm="connectService"
                :message="modalMessage"
                :state="connectModal"/>
            <ConfirmationModal
                v-on:cancel="disconnectModal = false"
                v-on:confirm="disconnectService"
                :message="modalMessage"
                :state="disconnectModal"/>
            <ConfirmationModal
                v-on:confirm="deleteService"
                v-on:cancel="deleteModal = false"
                :message="modalMessage"
                :state="deleteModal"/>
            <OneTimeServiceModal
                :key="tempModalKey + 78797897"
                :service="service_id"
                :connection="connection_id"
                :state="oneTimeServiceModal"
                v-on:onSale="onSale"
                v-on:onClose="oneTimeServiceModal = false; tempModalKey++"/>
            <HistoryModal
                :key="historyModalKey"
                :connection="connection_id"
                :state="historyModal"
                v-on:closeModal="historyModal = false"/>
        </v-card>
    </div>
</template>

<script>
    import ConfirmationModal from "../../components/Modals/ConfirmationModal/ConfirmationModal";
    import ClientBio from "../../components/Client/ClientBio/ClientBio";
    import OneTimeServiceModal from "../../components/Modals/OneTimeServiceModal/OneTimeServiceModal";
    import HistoryModal from "../../components/Modals/HistoryModal/HistoryModal";
    import ACTIONS from "../../store/actions";
    import GETTERS from "../../store/getters";
    import showToast from "../../utils/Toast";

    export default {
        async beforeRouteLeave(to, from, next) {
            await this.$store.dispatch(ACTIONS.CLEAR_CLIENT);
            next();
        },
        components: {
            ClientBio, ConfirmationModal, OneTimeServiceModal, HistoryModal
        },
        filters: {
            accountPipe(_value) {
                let value = _value;
                value = value.split('');
                let output = '';
                value.forEach((item, index) => {
                    output += item;
                    if (index % 2 !== 0) {
                        output += " ";
                    }
                });

                return output;
            }
        },
        created() {
            const id = this.$route.params.userId;
            this.$store.dispatch(ACTIONS.GET_CLIENT, id)
        },
        computed: {
            client() {
                return this.$store.getters[GETTERS.CLIENT];
            },
            subjects() {
                return this.$store.getters.getSubjects;
            },
        },
        data: () => ({
            tempModalKey: 0,
            editingConnection: null,
            editConnectionMode: false,
            service_id: null,
            historyModalKey: 0,
            connection_id: null,
            historyModal: false,
            balance: null,
            oneTimeServiceModal: false,
            showConfirmationModal: false,
            connectModal: false,
            disconnectModal: false,
            deleteModal: false,
            modalMessage: '',
            paymentMode: false,
            editMode: false,
            destroy: false,
        }),
        methods: {
            showTempServiceModal(item) {
                this.oneTimeServiceModal = true;
                this.connection_id = item.id;
                this.service_id = item.service_id;
            },
            onEdit() {
                this.editMode = true;
            },
            onSave(e) {
                this.editMode = false;
            },
            onSale() {
                showToast('Разовая услуга успешно продана!');
                this.oneTimeServiceModal = false;
                this.tempModalKey++;
            },
            triggerBalanceField(item) {
                this.client.connections = this.client.connections.map(c => {
                    if (c.id === item.id) {
                        c.paymentMode = true;
                    }
                    return c;
                });

            },
            cancelBalance(item) {
                this.balance = null;
                this.client.connections = this.client.connections.map(c => {
                    if (c.id === item.id) {
                        c.paymentMode = false;
                    }
                    return c;
                });
                return 0;
            },
            async addBalance(item) {
                if (this.balance < 1) {
                    showToast('Введенное число должно быть больше 0!', '', 'warning');
                    this.client.connections = this.client.connections.map(c => {
                        if (c.id === item.id) {
                            c.paymentMode = false;
                        }
                        return c;
                    });
                    return;
                }
                await this.$store.dispatch(ACTIONS.ADD_BALANCE, {
                    id: item.id,
                    balance: this.balance
                });
                this.balance = null;
                this.client.connections = this.client.connections.map(c => {
                    if (c.id === item.id) {
                        c.paymentMode = false;
                    }
                    return c;
                });
            },
            showDisconnectModal(service) {
                this.connection_id = service.id;
                this.showConfirmationModalFunction('Вы действительно хотите отключить выбранную услугу?');
                this.disconnectModal = true;
            },
            showHistoryModal(service) {
                this.connection_id = service.id;
                this.historyModalKey++;
                this.historyModal = true;
            },
            showDeleteModal(service) {
                this.connection_id = service.id;
                this.showConfirmationModalFunction('Вы действительно хотите удалить выбранную услугу?');
                this.deleteModal = true;
            },
            showConnectModal(service) {
                this.connection_id = service.id;
                this.showConfirmationModalFunction('Вы действительно хотите подключить выбранную услугу?');
                this.connectModal = true;
            },
            showPaymentModal(service) {
                this.showConfirmationModalFunction('Вы действительно хотите оплатить выбранную услугу?')
            },
            showConfirmationModalFunction(message) {
                this.modalMessage = message;
            },
            async disconnectService() {
                await this.$store.dispatch(ACTIONS.DISCONNECT, this.connection_id);
                this.disconnectModal = false;
                this.connection_id = null;
            },
            async connectService() {
                await this.$store.dispatch(ACTIONS.CONNECT, this.connection_id);
                this.connectModal = false;
                this.connection_id = null;
            },
            async deleteService() {
                this.deleteModal = false;
                await this.$store.dispatch(ACTIONS.DELETE_CONNECTION, this.connection_id);
                this.connection_id = null;
            },
            async enableEditMode(e) {
                this.editConnectionMode = true;
                this.editingConnection = e;
            },
            async editConnection() {
                await this.$store.dispatch(ACTIONS.EDIT_CONNECTION, {
                    id: this.editingConnection.id,
                    trademark: this.editingConnection.trademark,
                    personal_account: this.editingConnection.personal_account,
                    price: this.editingConnection.price,
                    address: this.editingConnection.address
                });
                this.editConnectionMode = false;
                this.editingConnection = null;
            },
            async onEditClick(e) {
                if (!this.editConnectionMode) {
                    this.enableEditMode(e);
                } else {
                    this.editConnection();
                }
            }
        }
    }
</script>

<style scoped lang="scss">
    @import "Client";
</style>
