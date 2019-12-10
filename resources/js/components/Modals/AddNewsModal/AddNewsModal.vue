<template>
    <v-dialog v-model="state" max-width="800" persistent>
        <v-card>
            <v-card-title class="headline blue darken-1 justify-content-between">
                <span class="white--text">{{ title }}</span>
                <v-btn icon text class="float-right" @click="$emit('onClose')">
                    <v-icon color="white">
                        mdi-close
                    </v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form v-if="!loading" class="p-2">
                    <v-text-field label="Заголовок" v-model="stock.title"></v-text-field>
                    <v-radio-group v-model="stockType">
                        <v-radio
                            key="1"
                            label="Акция"
                            value="stock"
                        />
                        <v-radio
                            key="2"
                            label="Баннер"
                            value="banner"
                        />
                    </v-radio-group>
                    <div v-if="stockType === 'stock'">
                        <ckeditor :editor="editor" :config="editorConfig" v-model="stock.body" rows="10"></ckeditor>
                    </div>
                    <v-select
                        v-if="stockType === 'banner'"
                        :items="services"
                        item-text="name"
                        item-value="id"
                        v-model="stock.service_id"
                    />
                    <input type="file" ref="fileInput" class="d-none" @change="uploadPhoto">
                    <img :src="'../storage/' + stock.image" alt="" v-if="stock.image" class="mt-2 mb-2" style="max-width: 400px; height: auto;">
                    <div>
                        <v-btn color="primary" max-width="400" class="mt-4" @click="choosePhoto">Загрузите фото
                            <v-icon class="ml-2">mdi-upload</v-icon>
                        </v-btn>
                    </div>
                </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions v-if="!loading">
                <v-btn text @click="$emit('onClose')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text @click="onSubmit" color="success" :disabled="stock.image === null"><b>{{okButton}}</b>
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
    import ClassicEditor from '@ckeditor/ckeditor5-build-classic';
    import '@ckeditor/ckeditor5-build-classic/build/translations/ru';
    import uploadFile from "../../../api/service/upload";
    import ACTIONS from "../../../store/actions";

    export default {
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            editMode: {
                type: Boolean,
                default: false
            },
            title: {
                type: String,
                default: 'Добавление новости'
            },
            okButton: {
                type: String,
                default: 'Создать'
            },
            serviceId: {
                type: Number,
                default: null
            }
        },
        watch: {
            async state(current) {
                if (current && this.editMode && this.serviceId !== null) {
                    const stock = await this.$store.getters.stock(this.serviceId);
                    this.stock.title = stock.title;
                    this.stock.body = stock.body || '';
                    this.stock.image = stock.image;
                    this.stockType = stock.service_id == null ? 'stock' : 'banner';
                    this.stock.service_id = stock.service_id;
                }
            }
        },
        data: () => ({
            loading: false,
            editor: ClassicEditor,
            stockType: 'stock',
            editorConfig: {
                language: 'ru',
                height: 700,
                autoGrow_minHeight: 400
            },
            stock: {
                title: '',
                body: '',
                image: null,
                service_id: null
            }
        }),
        computed: {
            services() {
                return this.$store.getters.getMobileServices;
            },
        },
        methods: {
            async onSubmit() {
                if (this.editMode) {
                    await this.editStock();
                } else {
                    await this.createStock();
                }
            },
            async editStock() {
                this.loading = true;
                this.stock.service_id = this.stockType === 'banner' ? this.stock.service_id : null;
                this.stock.body = this.stockType === 'stock' ? this.stock.body : null;
                await this.$store.dispatch(ACTIONS.EDIT_STOCK, {
                    id: this.serviceId,
                    image: this.stock.image,
                    body: this.stock.body || null,
                    title: this.stock.title,
                    service_id: this.stock.service_id,
                });
                this.$emit('onSave');
                this.clearData();
            },
            async createStock() {
                this.loading = true;
                this.stock.service_id = this.stockType === 'banner' ? this.stock.service_id : null;
                this.stock.body = this.stockType === 'stock' ? this.stock.body : null;
                await this.$store.dispatch(ACTIONS.CREATE_STOCK, {
                    image: this.stock.image,
                    body: this.stock.body,
                    title: this.stock.title,
                    service_id: this.stock.service_id,
                });
                this.$emit('onSave');
                this.clearData();
            },
            choosePhoto() {
                this.$refs.fileInput.click();
            },
            async uploadPhoto(e) {
                const result = await uploadFile(e.target.files[0]);
                this.stock.image = result.data;
            },
            clearData() {
                this.stock = {
                    title: '',
                    body: '',
                    image: null,
                    service_id: null
                };

                this.stockType = 'stock';

                this.loading = false;

            }
        }
    }
</script>

<style scoped>

</style>
