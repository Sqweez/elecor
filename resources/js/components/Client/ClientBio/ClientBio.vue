<template>
    <div>
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
        <v-form v-else v-model="valid">
            <v-row>
                <v-col col="4">
                    <div class="d-flex">
                        <p><span class="font-weight-black">СПО: </span>
                            <span v-if="!editMode">{{ client.type }}</span>
                        </p>
                        <v-select
                            v-if="editMode"
                            v-model="client.client_type"
                            :items="client_types"
                            class="subject__select"
                            item-text="type"
                            item-value="id"></v-select>
                    </div>
                    <div class="d-flex">
                        <p><span class="font-weight-black">Контрагент: </span>
                            <span v-if="!editMode">{{ client.name }}</span>
                        </p>
                        <v-text-field v-if="editMode" class="subject__select"
                                      :rules="nameRules"
                                      v-model="client.name"></v-text-field>
                    </div>
                    <div class="d-flex">
                        <p><span class="font-weight-black">Телефон:</span></p>
                        <p class="ml-2">
                        <span v-if="!editMode" v-for="(phone, idx) of client.phones" :key="`phone-${idx}`">
                            {{ phone }}<br>
                        </span>
                        </p>
                        <div v-if="editMode">
                            <component
                                :rules="phoneRules"
                                label="Телефон"
                                class="subject__select"
                                v-for="(phoneInput, index) of phoneInputs"
                                :key="index"
                                v-mask="'#-###-###-##-##'"
                                append-outer-icon="mdi-plus"
                                @click:append-outer="addPhoneInput"
                                v-model="client.phones[index]"
                                :is="phoneInput.component">
                            </component>
                            <v-btn
                                color="primary"
                                class="ml-2"
                                v-if="phoneInputs.length > 1"
                                @click="removePhoneInput"
                            >
                                Удалить поле
                                <v-icon>mdi-delete</v-icon>
                            </v-btn>
                        </div>
                    </div>
                    <div class="d-flex" v-if="client.physical_person">
                        <p><span class="font-weight-black">Пол: </span>
                            <span v-if="!editMode">
                                {{ client.gender_text }}
                            </span>
                        </p>
                        <v-select
                            v-if="editMode"
                            v-model="client.gender"
                            :items="genders"
                            class="subject__select"
                            item-text="gender"
                            item-value="id"></v-select>
                    </div>
                    <div class="d-flex" v-if="!editMode">
                        <p>
                            <span class="font-weight-black">
                                Бонусный счет:
                            </span>
                            <span>{{ client.bonuses | bonus}}</span>
                        </p>
                    </div>
                    <div class="d-flex" v-if="client.physical_person">
                        <p><span class="font-weight-black">Дата рождения: </span>
                            <span v-if="!editMode">
                                {{ getDate(client.birth_date) }}
                            </span>
                        </p>
                        <v-text-field label="Дата рождения" type="date" v-model="client.birth_date" v-if="editMode"/>
                    </div>
                    <div class="d-flex" v-if="client.physical_person">
                        <p><span class="font-weight-black">Язык: </span>
                            <span v-if="!editMode">
                                {{ client.lang_text }}
                            </span>
                        </p>
                        <v-select
                            v-if="editMode"
                            v-model="client.lang"
                            :items="languages"
                            class="subject__select"
                            item-text="lang"
                            item-value="id"></v-select>
                    </div>
                    <div class="d-flex">
                        <p><span class="font-weight-black">Комментарий: </span>
                            <span v-if="!editMode">{{ client.comment }}</span>
                        </p>
                        <v-textarea v-if="editMode" class="subject__select" v-model="client.comment"
                                    auto-grow></v-textarea>
                    </div>
                </v-col>
                <v-col cols="4" v-if="user.role_id !== 2">
                    <v-row justify="center">
                        <div>
                            <v-avatar size="200" tile v-if="newPhoto || client.photo">
                                <v-img :src="'../storage/' + client.photo + '?' + dummy" alt="аватар" contain
                                       v-show="!newPhoto"/>
                                <v-img :src="'../storage/' + newPhoto + '?' + dummy" alt="аватар" contain
                                       v-show="newPhoto"/>
                            </v-avatar>
                            <div>
                                <input type="file" name="photo" class="d-none" ref="photoInput" @change="uploadPhoto">
                                <v-btn block color="primary" v-if="editMode" class="change-photo" @click="choosePhoto">
                                    Сменить фото
                                </v-btn>
                            </div>
                        </div>
                    </v-row>
                </v-col>
                <v-col cols="4">
                    <div class="button-container">
                        <v-btn block :color="editMode ? 'success' : 'primary'" @click="editUser" :disabled="!valid"
                               v-if="user.role_id === 1 || user.role_id === 3">
                            <span v-if="!editMode">Редактировать</span>
                            <span v-else>Сохранить</span>
                            <v-icon v-if="editMode">mdi-check</v-icon>
                        </v-btn>
                        <v-btn block v-if="editMode" @click="deleteModal = true" color="red darken-2" class="mt-5">
                            <span class="white--text">
                                Удалить клиента
                                <v-icon>mdi-delete</v-icon>
                            </span>
                        </v-btn>
                    </div>
                    <div class="button-container" v-if="!editMode && (user.role_id === 1 || user.role_id === 3)">
                        <v-btn block color="primary" @click="showConnectModal">Новая услуга</v-btn>
                    </div>
                    <div class="button-container" v-if="!editMode && user.role_id !== 2">
                        <v-btn block color="primary" @click="showPushModal">Отправить пуш</v-btn>
                    </div>
                    <div class="d-flex align-center justify-content-center my-3 mx-auto qr-container" v-if="hasQr">
                        <v-progress-circular
                            indeterminate
                            v-if="qrLoading"
                            size="65"
                            color="primary"
                        ></v-progress-circular>
                       <span v-html="qrCode" v-if="qrCode" id="qr-code"></span>
                    </div>
                    <div class="button-container" v-if="!editMode && qrCode">
                        <v-btn block color="primary" @click="printQrCode">Печать</v-btn>
                    </div>
                    <div class="button-container" v-if="!editMode">
                        <v-btn block color="primary" @click="bonusOperationModal = true">Операции с бонусами</v-btn>
                    </div>
                </v-col>
                <message-modal
                    v-if="user.role_id !== 2"
                    :state="showSendModal"
                    v-on:sendMessage="sendMessage"
                    title="Отправка пуш-уведомления"
                    v-on:modalClosed="closeModal"/>
                <ConnectServiceModal
                    v-if="user.role_id !== 2"
                    :key="connectKey"
                    :state="connectModal"
                    v-on:cancel="closeConnectModal"
                    v-on:connect="onConnectModal"/>
                <ConfirmationModal
                    v-if="user.role_id !== 2"
                    :state="deleteModal"
                    v-on:cancel="deleteModal = false"
                    v-on:confirm="deleteClient"
                    :message="`Вы действительно хотите удалить клиента ${client.name}?`"
                />
                <BonusOperationsModal
                    :state="bonusOperationModal"
                    @close="bonusOperationModal = false"
                />
            </v-row>
        </v-form>
    </div>
</template>

<script>
    import MessageModal from "../../Modals/MessageModal/MessageModal";
    import ConnectServiceModal from "../../Modals/ConnectServiceModal/ConnectServiceModal";
    import {VTextField} from 'vuetify/lib';
    import uploadFile from "../../../api/service/upload";
    import ACTIONS from "../../../store/actions";
    import ConfirmationModal from "../../Modals/ConfirmationModal/ConfirmationModal";
    import showToast from "../../../utils/Toast";
    import {getQRCode, sendPushToClient} from "../../../api/client/clientApi";
    import GETTERS from "../../../store/getters";
    import BonusOperationsModal from "../../Modals/BonusOperations/BonusOperationsModal";

    export default {
        components: {
            BonusOperationsModal,
            MessageModal, ConnectServiceModal, VTextField, ConfirmationModal
        },
        async mounted () {
            await this.fillPhones();
            await this.getQr();
        },
        data: () => ({
            connectKey: 0,
            bonusOperationModal: false,
            dummy: Date.now(),
            deleteModal: false,
            valid: true,
            connectModal: false,
            showSendModal: false,
            editMode: false,
            newPhoto: '',
            addFields: [],
            qrCode: null,
            hasQr: true,
            qrLoading: true,
            phoneInputs: [],
            nameRules: [
                v => !!v || 'Требуется ввести контрагента'
            ],
            phoneRules: [
                v => !!v || 'Требуется ввести телефон'
            ],
        }),
        computed: {
            user() {
                return this.$store.getters.user;
            },
            genders() {
                return this.$store.getters.GENDERS;
            },
            languages() {
                return this.$store.getters.LANGUAGES;
            },
            client() {
                return this.$store.getters[GETTERS.CLIENT];
            },
            activeFields() {
                return this.$store.getters.active_fields;
            },
            client_types() {
                return this.$store.getters[GETTERS.CLIENT_TYPES]
            }
        },
        methods: {
            async getQr() {
                try {
                    const response = await getQRCode(this.client.id);
                    this.qrCode = response.data;
                } catch (e) {
                    this.hasQr = false;
                } finally {
                    this.qrLoading = false;
                }

            },
            async fillPhones() {
                this.phoneInputs = this.client.phones.map(c => {
                    return {
                        component: VTextField
                    };
                });
            },
            toggleEdit() {
                this.editMode = true;
                this.$emit('editToggled', {});
            },
            getDate(date) {
                if (date !== '0000-00-00' && date !== null) {
                    return new Date(date).toLocaleDateString()
                } else {
                    return 'Не указано'
                }
            },
            async saveUser() {
                this.client.phones = this.client.phones
                    .filter(phone => !!phone)
                    .map(phone => phone.replaceAll('-', ''));
                await this.$store.dispatch(ACTIONS.EDIT_CLIENT, {
                    client: this.client,
                    newPhoto: this.newPhoto,
                });
                await this.getQr();
                await this.fillPhones();
                this.newPhoto = '';
                this.editMode = false;
                this.$emit('saveToggled', this.client);
            },
            showConnectModal() {
                this.connectModal = true;
            },
            closeConnectModal() {
                this.connectKey++;
                this.connectModal = false;
            },
            onConnectModal() {
                this.connectKey++;
                this.connectModal = false;
                showToast('Услуга была успешно подключена');
            },
            closeModal() {
                this.showSendModal = false;
            },
            async deleteClient() {
                await this.$store.dispatch(ACTIONS.DELETE_CLIENT, this.client.id);
                await this.$router.push('/');
            },
            editUser() {
                if (this.editMode) {
                    this.saveUser();
                } else {
                    this.toggleEdit();
                }
            },
            addPhoneInput() {
                this.client.phones.push('');
                this.phoneInputs.push({component: VTextField})
            },
            removePhoneInput() {
                this.client.phones.pop();
                this.phoneInputs.pop()
            },
            choosePhoto() {
                this.$refs.photoInput.click();
            },
            async uploadPhoto(e) {
                const file = e.target.files[0];
                const result = await uploadFile(file);
                this.newPhoto = result.data;
            },
            async sendMessage(message) {
                const _message = {
                    ...message,
                    client_id: this.client.id,
                    user_id: 1,
                };
                const response = await sendPushToClient(_message);
                this.showSendModal = false;
                showToast('Сообщение отправлено');
            },
            showPushModal() {
                if (this.client.has_push_token) {
                    this.showSendModal = true;
                    return;
                }
                showToast('Данный клиент не установил мобильное приложение: отправка пуш-уведомления невозможна', 'Ошибка', 'warning')
            },
            printQrCode() {
                const qrCode = document.querySelector('#qr-code');
                const printWindow = window.open();
                printWindow.document.write(`<html><head><title>QR-код - ${this.client.name}</title>`);
                printWindow.document.write('</head><body>');
                printWindow.document.write(qrCode.innerHTML);
                printWindow.document.write('</body></html>');
                printWindow.print();
                printWindow.close();
                return true;
            }
        }
    }
</script>

<style scoped>
    .d-flex {
        padding: 10px;
    }

    .subject__select {
        padding-top: 0 !important;
        margin-top: -2px !important;
        margin-left: 10px;
    }

    .change-photo {
        margin-top: 15px;
    }

    .button-container {
        margin: 10px auto;
        max-width: 300px;
    }

    p {
        font-size: 16px;
    }

    .qr-container {
        min-height: 230px;
    }
</style>
