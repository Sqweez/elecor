<template>
    <v-dialog max-width="900" v-model="state" persistent>
        <v-card>
            <v-card-title class="headline blue darken-1 justify-content-between">
                <span class="white--text">Добавление нового клиента</span>
                <v-btn icon text class="float-right" @click="closeModal">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-simple-table v-if="!loading && duplicates.length">
                    <template v-slot:default>
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Имя</th>
                            <th>Посмотреть</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-for="(d, index) of duplicates" :key="index">
                            <td>{{ ++index }}</td>
                            <td>{{ d.name }}</td>
                            <td>
                                <v-btn icon @click="openProfile(d)">
                                    <v-icon>mdi-eye</v-icon>
                                </v-btn>
                            </td>
                        </tr>
                        </tbody>
                    </template>
                </v-simple-table>
                <v-form class="p-2" v-if="!loading && !duplicates.length" v-model="valid">
                    <v-text-field label="Контрагент" v-model.trim="client.name" :rules="nameRules"></v-text-field>
                    <v-text-field label="Дата рождения" type="date" v-model="client.birth_date"
                                  :rules="dateRules"></v-text-field>
                    <v-text-field
                        label="Номер телефона"
                        append-outer-icon="mdi-plus"
                        v-mask="'#-###-###-##-##'"
                        clearable
                        v-model="client.phones[0]"
                        :rules="phoneRules"
                        @click:append-outer="addPhoneInput">
                    </v-text-field>
                    <component v-for="(phoneInput, index) of phoneInputs"
                               v-model="client.phones[index + 1]"
                               v-mask="'#-###-###-##-##'"
                               clearable
                               :is="phoneInput.component" :label="phoneInput.label"
                               :key="index"></component>
                    <input type="file" name="photo" class="d-none" ref="photoInput" @change="uploadPhoto">
                    <v-btn color="primary" max-width="400" @click="choosePhoto" v-if="!client.photo">
                        Загрузите фото
                        <v-icon class="ml-2">mdi-upload</v-icon>
                    </v-btn>
                    <v-img :src="'../storage/' + client.photo" alt="" v-if="client.photo" max-width="500"
                           max-height="300" contain/>
                    <v-select
                        :rules="clientTypeRules"
                        class="mt-3"
                        label="Тип клиента"
                        :items="subjects"
                        item-text="type"
                        v-model="client.client_type"
                        item-value="id"></v-select>
                    <v-textarea label="Комментарий"
                                v-model.trim="client.comment"
                                auto-grow rows="6"></v-textarea>
                </v-form>
            </v-card-text>
            <v-card-actions class="p-2" v-if="!loading">
                <v-btn text @click="closeModal">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn
                    v-if="!duplicates.length"
                    text
                    @click="checkDuplicates"
                    color="success"
                    :disabled="!valid">
                    <b>Создать</b>
                    <v-icon>mdi-check</v-icon>
                </v-btn>
                <v-btn
                    v-if="duplicates.length"
                    text
                    @click="createUser"
                    color="success"
                    >
                    <b>Все равно создать</b>
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
            <v-progress-linear
                indeterminate
                :active="loading"
                color="green"
                absolute
                bottom
            ></v-progress-linear>
        </v-card>
    </v-dialog>
</template>

<script>
    import {VTextField} from 'vuetify/lib'
    import SimpleLoader from "../../Loaders/SimpleLoader/SimpleLoader";
    import uploadFile from "@/api/service/upload";
    import moment from 'moment';
    import ACTIONS from "../../../store/actions";
    import GETTERS from "../../../store/getters";
    import showToast from "../../../utils/Toast";
    import {TOAST_TYPE} from "../../../config/consts";
    String.prototype.replaceAll = function (search, replacement) {
        let target = this;
        return target.split(search).join(replacement);
    };
    export default {
        components: {
            VTextField,
            SimpleLoader
        },
        data: () => ({
            valid: true,
            duplicates: [],
            client: {
                phones: [],
                name: '',
                birth_date: null,
                client_type: null,
                comment: '',
                photo: '',
            },
            showLoader: false,
            loading: false,
            phone: '',
            phoneInputs: [],
            phoneInputObject: {
                'label': 'Номер телефона',
                'component': VTextField,
            },
            nameRules: [
                v => !!v || 'Требуется ввести контрагента'
            ],
            dateRules: [
                /*v => !!v || 'Требуется ввести дату рождения',
                v => (!moment(v).isAfter(moment()) && !moment(v).isBefore(moment([1900, 0, 1]))) || 'Дата рождения не корректна',*/
            ],
            phoneRules: [
                v => !!v || 'Требуется ввести телефон'
            ],
            clientTypeRules: [
                v => !!v || 'Требуется выбрать тип клиента',
            ]
        }),
        props: {
            state: {
                type: Boolean,
                default: true,
            }
        },
        computed: {
            subjects() {
                return this.$store.getters.getSubjects;
            },
        },
        methods: {
            checkDuplicates() {
                this.loading = true;
                const duplicates = this.$store.getters[GETTERS.CLIENT_DUPLICATE](this.client);
                if (!duplicates.length) {
                    this.createUser();
                    return;
                }
                this.duplicates = duplicates;
                this.loading = false;
                showToast('Обнаружены похожие клиенты', 'Внимание!', TOAST_TYPE.WARNING);
            },
            openProfile(client) {
                const protocol = window.location.protocol;
                const hostName = window.location.host;
                const uri = `clients/${client.id}`;
                const url = `${protocol}//${hostName}/${uri}`;
                window.open(url, '_blank');
            },
            choosePhoto() {
                this.$refs.photoInput.click();
            },
            async uploadPhoto(e) {
                const result = await uploadFile(e.target.files[0]);
                this.client.photo = result.data;
            },
            addPhoneInput() {
                this.phoneInputs.push(this.phoneInputObject);
            },
            async createUser() {
                this.loading = true;
                console.log(this.client);
                this.client.phones = this.client.phones
                    .filter(phone => !!phone)
                    .map(phone => phone.replaceAll('-', ''));
                const response = await this.$store.dispatch(ACTIONS.CREATE_CLIENT, this.client);
                this.clearClient();
                this.loading = false;
                this.$emit('onSave', response)
            },
            clearClient() {
                this.duplicates = [];
                this.valid = true;
                this.client = {
                    phones: [],
                    name: '',
                    birth_date: null,
                    client_type: null,
                    comment: '',
                    photo: '',
                };
            },
            closeModal() {
                this.clearClient();
                this.$emit('onClose');
            }
        }
    }
</script>

