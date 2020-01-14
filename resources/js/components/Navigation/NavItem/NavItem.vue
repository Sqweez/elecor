<template>
    <router-link v-if="!menu.content" :to="menu.url" tag="li" class="nav-item" :class="{'nav-item-active': isActive}"
                 @mouseover="hover = true" @mouseleave="hover = false">
        <v-icon color="white">{{menu.icon}}</v-icon>
        <span>{{menu.title}}</span>
        <v-icon :class="{'rotate': hover}" color="white" v-if="menu.content">mdi-menu-down</v-icon>
        <div class="dropdown" v-if="menu.content && hover">
            <ul class="dropdown-list">
                <li class="dropdown-item" v-for="(content, index) in menu.content" :key="index">{{ content.title }}</li>
            </ul>
        </div>
    </router-link>
    <li v-else class="nav-item" :class="{'nav-item-active': isActive}" @mouseover="hover = true"
        v-show="!showMenu"
        @mouseleave="hover = false">
        <v-icon color="white">{{menu.icon}}</v-icon>
        <span>{{menu.title}}</span>
        <v-icon :class="{'rotate': hover}" color="white" v-if="menu.content">mdi-menu-down</v-icon>
        <div class="dropdown" v-if="menu.content && hover">
            <ul class="dropdown-list">
                <router-link
                    :to="content.url"
                    v-if="!content.hide || !content.hide.includes(user.role_id)"
                    tag="li"
                    class="dropdown-item"
                    v-for="(content, index) in menu.content"
                    :key="index">
                        {{ content.title }}
                </router-link>
            </ul>
        </div>
    </li>
</template>

<script>
    export default {
        data: () => ({
            hover: false,
        }),
        computed: {
            isActive() {
                if (this.menu.content) {
                    return this.menu.content.some(el => {
                        return this.$route.path === el.url;
                    })
                }
                if (this.menu.url === this.$route.path) {
                    return true;
                }
                return false;
            },
            user() {
                return this.$store.getters.user;
            },
            showMenu() {
                try {
                    return this.menu.content.every(s => {
                        return s.hide.includes(this.user.role_id);
                    });
                } catch (e) {
                    return false;
                }
            }
        },
        props: {
            menu: {
                type: Object,
                default: {}
            }
        }
    }
</script>

<style lang="scss" scoped>
    @import "NavItem";
</style>
