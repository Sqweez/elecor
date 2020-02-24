<template>
    <v-dialog max-width="800" persistent v-model="state">
        <v-card>
            <v-card-title class="blue darken-1 d-flex justify-content-between">
                <span class="white--text">{{ id === null ? 'Добавление' : 'Редактирование' }} шаблона</span>
                <v-btn icon @click="$emit('onClose')">
                    <v-icon color="white">mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form class="p-3" v-if="!loading">
                    <v-text-field v-model="name" label="Наименование шаблона"></v-text-field>
                    <v-text-field v-model="title" label="Заголовок"></v-text-field>
                    <v-textarea
                        auto-grow rows="5"
                        counter
                        v-model="body"
                        label="Текст"></v-textarea>
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
                    <v-divider></v-divider>
                    <v-flex class="mt-2">
                        <h3>Предпросмотр шаблона:</h3>
                        <h4>{{ this.title }}</h4>
                        <p>{{ this.message }}</p>
                    </v-flex>
                </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions v-if="!loading">
                <v-btn @click="$emit('onClose')" text>Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn @click="onSubmit" text color="success">
                    Сохранить
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
    import showToast from "../../../utils/Toast";

    String.prototype.replaceAll = function(search, replacement) {
        let target = this;
        return target.split(search).join(replacement);
    };
    export default {
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            id: {
                type: Number,
                default: null
            }
        },
        computed: {
            message() {
                let outputMessage = this.body;
                this.variables.forEach(v => {
                    outputMessage = outputMessage.replaceAll(v.key, v.example);
                });

                return outputMessage;
            }
        },
        watch: {
            state() {
                this.name = '';
                this.title = '';
                this.body = '';
                if (this.id !== null) {
                    const template = this.$store.getters.mailing_template(this.id);
                    this.name = template.name;
                    this.title = template.title;
                    this.body = template.body;
                }
            }
        },
        data: () => ({
            loading: false,
            name: '',
            title: '',
            body: '',
            variables: [
                {
                    key: '%ИМЯ%',
                    name: 'Имя клиента',
                    example: 'Александр Андреевич'
                },
                /*{
                    key: '%ДОЛГ%',
                    name: 'Задолженность',
                    example: '5000'
                },*/
            ],
        }),
        methods: {
            async onSubmit() {
              this.loading = true;
              if (this.id === null) {
                  await this.createTemplate();
              } else {
                  await this.editTemplate();
              };
              this.$emit('onClose');
              this.loading = false;
            },
            async createTemplate() {
                const template = {
                    title: this.title,
                    body: this.body,
                    name: this.name
                };

                await this.$store.dispatch('createMailingTemplates', template);

                showToast('Шаблон успешно создан!');
            },
            async editTemplate() {
                const template = {
                    id: this.id,
                    title: this.title,
                    body: this.body,
                    name: this.name
                };

                await this.$store.dispatch('editMailingTemplates', template);

                showToast('Шаблон успешно отредактирован!');
            },
            addVariableToTemplate(variable) {
                this.body += variable.key;
            }
        }
    }
</script>

<style scoped>

</style>
