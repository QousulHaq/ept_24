<template>
  <span>{{timeFormatted}}</span>
</template>

<script>

  export default {
    name: "Countdown",
    props: {
      availableTime: {
        required: true,
        type: Number
      },
      gap: {
        type: Number,
        default: 5
      },
      freeze: {
        type: Boolean,
        default: false
      }
    },
    data: () => ({
      time: 0,
    }),
    computed: {
      timeFormatted: function () {
        function sec2time(timeInSeconds) {
          const pad = (num, size) => ('000' + num).slice(size * -1),
            time = parseInt(timeInSeconds),
            minutes = Math.floor(time / 60) % 60,
            seconds = Math.floor(time - minutes * 60)

          return pad(minutes, 2) + ':' + pad(seconds, 2)
        }
        return sec2time(this.time)
      }
    },
    watch: {
      availableTime: function (newValue) {
        this.time = newValue
      },
      time: function (newValue) {
        if (newValue % this.gap === 0) {
          this.$emit('every', this.gap)
        }

        if (newValue <= 0) {
          this.$emit('timeout')
        }
      }
    },
    methods: {
      decrementTime () {
        this.time > 0 ? ! this.freeze ? this.time-- : null : null

        // call recursively after 1 second
        setTimeout(() => this.decrementTime(), 1000)
      },
      reset () {
        this.time = this.availableTime
      }
    },
    mounted () {
      this.decrementTime()
    }
  }
</script>

<style scoped>

</style>
