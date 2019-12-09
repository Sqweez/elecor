<template>
    <v-overlay v-model="state">
        <v-progress-circular indeterminate size="80" color="blue darken-1" width="7"></v-progress-circular>
    </v-overlay>
</template>

<script>
    export default {
        props: {
            state: {
                type: Boolean,
                default: false,
            }
        }
    }
</script>
