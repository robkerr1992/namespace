<template>
    <!-- default loadding spinner -->
    <svg
        v-if="!template || template === 'small'"
        v-bind="$attrs"
        class="animate-spin"
        :class="[
      color, size
    ]"
        xmlns="http://www.w3.org/2000/svg"
        fill="none"
        viewBox="0 0 24 24"
    >
        <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
        />
        <path
            class="opacity-75"
            fill="currentColor"
            stroke-width="1"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
        />
    </svg>
    <div
        v-if="template === 'large'"
        class="loader ease-linear rounded-full border-8 border-t-8 border-gray-800"
        :class="[ color, size ]"
    />
</template>

<script>
export default {
    props: {
        template: {
            type: String,
            default: 'small'
        },
        color: {
            type: String,
            default: 'text-gray-200 dark:text-midnight-600'
        },
        size: {
            type: String,
            default: 'h-5 w-5'
        }
    }
}
</script>
<style lang="css" scoped>
.loader {
    border-top-color: #eeeeee;
    -webkit-animation: spinner 1s linear infinite;
    animation: spinner 1s linear infinite;
}
@-webkit-keyframes spinner {
    0% { -webkit-transform: rotate(0deg); }
    100% { -webkit-transform: rotate(360deg); }
}
@keyframes spinner {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}
</style>
