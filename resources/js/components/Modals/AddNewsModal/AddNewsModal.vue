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
                    <v-text-field label="Заголовок" v-model="currentStock.title"></v-text-field>
                    <ckeditor :editor="editor" :config="editorConfig" v-model="currentStock.body" rows="10"></ckeditor>
                    <input type="file" ref="fileInput" class="d-none" @change="uploadPhoto">
                    <img :src="'../storage/' + currentStock.image" alt="" v-if="currentStock.image" class="mt-2 mb-2" style="max-width: 400px; height: auto;">
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
                <v-btn text @click="onSubmit" color="success"><b>{{okButton}}</b>
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
            currentStock: {
                type: Object,
                default: () => ({
                    title: '',
                    body: '',
                    image: null,
                })
            }
        },
        data: () => ({
            loading: false,
            editor: ClassicEditor,
            editorConfig: {
                language: 'ru',
                height: 700,
                autoGrow_minHeight: 400
            }
        }),
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
                await this.$store.dispatch(ACTIONS.EDIT_STOCK, {
                    id: this.currentStock.id,
                    image: this.currentStock.image,
                    body: this.currentStock.body,
                    title: this.currentStock.title,
                });
                this.$emit('onSave')
            },
            async createStock() {
                this.loading = true;
                await this.$store.dispatch(ACTIONS.CREATE_STOCK, {
                    image: this.currentStock.image,
                    body: this.currentStock.body,
                    title: this.currentStock.title,
                });
                this.$emit('onSave')
            },
            choosePhoto() {
                this.$refs.fileInput.click();
            },
            async uploadPhoto(e) {
                const result = await uploadFile(e.target.files[0]);
                this.currentStock.image = result.data;
            }
        }
    }
</script>

<style scoped>

</style>
