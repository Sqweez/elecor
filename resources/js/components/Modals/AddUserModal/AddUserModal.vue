<template>
    <v-dialog persistent max-width="700" v-model="state">
        <v-card>
            <v-card-title class="d-flex justify-content-between blue darken-1">
                <span class="white--text">{{ title }}</span>
                <v-btn icon @click="$emit('onClose')" color="white">
                    <v-icon>mdi-close</v-icon>
                </v-btn>
            </v-card-title>
            <v-card-text>
                <v-form v-if="!loading">
                    <v-text-field label="Имя" v-model="user.displayName"></v-text-field>
                    <v-text-field label="Логин" v-model="user.login"></v-text-field>
                    <v-text-field label="Пароль" v-model="user.password"></v-text-field>
                    <v-select
                        label="Роль"
                        :items="roles"
                        v-model="user.role"
                    />
                </v-form>
            </v-card-text>
            <v-divider></v-divider>
            <v-card-actions v-if="!loading">
                <v-btn text @click="$emit('onClose')">Отмена</v-btn>
                <v-spacer></v-spacer>
                <v-btn text @click="onSave" color="success">
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
    export default {
        mounted() {
          if (this.editMode) {
              this.user.displayName = 'Администратор';
              this.user.login = 'elecor-kuzet';
              this.user.password = '123456';
              this.user.role = 'Суперпользователь';
          }
        },
        methods: {
            onSave() {
                this.loading = true;
                setTimeout(() => {
                    this.$emit('onSave')
                }, 3000);
            },
        },
        data: () => ({
            loading: false,
            user: {
                displayName: '',
                login: '',
                password: '',
                role: null
            },
            roles: [
                'Суперпользователь', 'Администратор', 'Кассир',
            ],
        }),
        props: {
            state: {
                type: Boolean,
                default: false,
            },
            title: {
                type: String,
                default: 'Добавление пользователя'
            },
            editMode: {
                type: Boolean,
                default: false
            }
        }
    }
</script>

<style scoped>

</style>
