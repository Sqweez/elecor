<template>
    <v-dialog max-width="800" persistent v-model="state">
        <v-card>
            <v-card-title class="blue darken-1 d-flex justify-content-between">
                <span class="white--text">{{ title }}</span>
                <v-btn icon @click="$emit('onClose')">
                    <v-icon color="white">mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form class="p-3" v-if="!loading">
                    <v-text-field v-model="name" label="Наименование шаблона"></v-text-field>
                    <v-text-field v-model="header" label="Заголовок"></v-text-field>
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
                        <h4>{{ this.header }}</h4>
                        <p>{{ this.message }}</p>
                    </v-flex>
                </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions v-if="!loading">
                <v-btn @click="$emit('onClose')" text>Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn @click="onSave" text color="success">
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
            title: {
                type: String,
                default: 'Добавление шаблона'
            },
            editMode: {
                type: Boolean,
                default: false
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
        mounted() {
          if (this.editMode) {
              this.header = 'Внимание!';
              this.name = 'Долговой шаблон';
              this.body = 'Уважаемый, %ИМЯ% оплатите долг, пожалуйста!'
          }
        },
        data: () => ({
            loading: false,
            name: '',
            header: '',
            body: '',
            variables: [
                {
                    key: '%ИМЯ%',
                    name: 'Имя клиента',
                    example: 'Александр Андреевич'
                },
                {
                    key: '%ДОЛГ%',
                    name: 'Задолженность',
                    example: '5000'
                },
            ],
        }),
        methods: {
            onSave() {
                this.loading = true;
                setTimeout(() => {
                    this.$emit('onSave');
                }, 3000);
            },
            addVariableToTemplate(variable) {
                this.body += variable.key;
            }
        }
    }
</script>

<style scoped>

</style>
