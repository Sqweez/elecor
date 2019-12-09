<template>
    <v-dialog max-width="900" persistent v-model="state">
        <v-card>
            <v-card-title class="blue darken-1 d-flex justify-content-between">
                <span class="white--text">{{ editMode ? 'Редактирование услуги' : 'Добавление услуги'}}</span>
                <v-btn icon @click="closeModal">
                    <v-icon color="white">mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form class="p-3">
                    <v-text-field label="Наименование услуги" v-model="name"/>
                    <ckeditor :editor="editor" :config="editorConfig" v-model="description" rows="10"
                              class="mb-3"></ckeditor>
                    <input type="file" class="d-none" ref="iconInput" @change="uploadIcon">
                    <img :src="'../storage/' + icon" alt="" v-if="icon" class="mt-2 mb-2"
                         style="max-width: 50px; height: auto;">
                    <div class="mt-2 mb-2">
                        <v-btn color="primary" @click="$refs.iconInput.click()" style="width: 280px;">
                            Загрузить иконку
                            <v-icon class="ml-2">mdi-upload</v-icon>
                        </v-btn>
                    </div>
                    <input type="file" class="d-none" ref="imageInput" @change="uploadPhoto">
                    <img :src="'../storage/' + image" alt="" v-if="icon" class="mt-2 mb-2"
                         style="max-width: 400px; height: auto;">
                    <div class="mt-2 mb-2">
                        <v-btn color="primary" @click="$refs.imageInput.click()" style="width: 280px;">
                            Загрузить изображение
                            <v-icon class="ml-2">mdi-upload</v-icon>
                        </v-btn>
                    </div>
                    <v-checkbox label="Разовая услуга" v-model="isOneTime" @change="select_key++"/>
                    <v-select
                        label="Привязать к услуге"
                        item-text="name"
                        item-value="id"
                        :items="mainServices"
                        :key="select_key"
                        v-model="main_id"
                        v-show="!!isOneTime"
                    />
                    <h4>Дополнительные поля:</h4>
                    <div class="d-flex align-items-center">
                        <div style="flex-grow: 1">
                            <div class="d-flex justify-content-between" v-for="(field, key) of additional_information"
                                 :key="key">
                                <v-text-field label="Наименование поля" v-model="field.key"/>
                                <v-text-field label="Значение поля" v-model="field.value"/>
                            </div>
                        </div>
                        <v-btn icon @click="addField">
                            <v-icon>mdi-plus</v-icon>
                        </v-btn>
                    </div>

                </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-btn text @click="closeModal">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text color="success" @click="saveService">
                    Сохранить
                    <v-icon>mdi-check</v-icon>
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<script>
    import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
    import '@ckeditor/ckeditor5-build-classic/build/translations/ru';
    import uploadFile from "../../../api/service/upload";
    import ACTIONS from "../../../store/actions";

    export default {
        data: () => ({
            name: '',
            description: '',
            icon: null,
            image: null,
            main_id: null,
            isOneTime: false,
            select_key: 0,
            additional_information: [{
                key: '',
                value: '',
            }],
            editor: ClassicEditor,
            editorConfig: {
                language: 'ru',
                height: 700,
                autoGrow_minHeight: 400
            }
        }),
        computed: {
            mainServices() {
                return this.$store.getters.getMobileServices.filter(s => !s.main_id && s.id !== this.id);
            },
        },
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            editMode: {
                type: Boolean,
                default: false
            },
            id: {
                type: Number,
                default: null,
            }
        },
        watch: {
            state(oldValue, currentValue) {
                if (oldValue && this.editMode) {
                    const service = this.$store.getters.getMobileService(this.id);
                    this.name = service.name;
                    this.icon = service.icon;
                    this.main_id = service.main_id;
                    this.isOneTime = this.main_id !== null;
                    this.image = service.image;
                    this.description = service.description;
                    this.additional_information = JSON.parse(service.additional_information) || [{key: '', value: ''}];
                }
            }
        },
        methods: {
            async uploadIcon(e) {
                const {data} = await uploadFile(e.target.files[0]);
                this.icon = data;
            },
            async uploadPhoto(e) {
                const {data} = await uploadFile(e.target.files[0]);
                this.image = data;
            },
            closeModal() {
                this.name = '';
                this.description = '';
                this.icon = null;
                this.image = null;
                this.main_id = null;
                this.isOneTime = false;
                this.additional_information = [{
                    key: '',
                    value: '',
                }];
                this.select_key++;
                this.$emit('onClose');
            },
            addField() {
                this.additional_information.push({key: '', value: ''});
            },
            async saveService() {
                if (!this.isOneTime) {
                    this.main_id = null;
                }
                this.additional_information = this.additional_information.filter(s => s.key && s.value);
                const service = {
                    name: this.name,
                    icon: this.icon,
                    image: this.image,
                    description: this.description,
                    main_id: this.main_id,
                    additional_information: JSON.stringify(this.additional_information)
                };

                if (!this.editMode) {
                    await this.$store.dispatch(ACTIONS.CREATE_MOBILE_SERVICE, service);
                } else {
                    service.id = this.id;
                    await this.$store.dispatch(ACTIONS.EDIT_MOBILE_SERVICE, service);
                }

                this.closeModal();
            }
        }
    }
</script>

<style scoped>
</style>
