<template>
  <div id="audio-form">
    <div class="form-group">
      <label>Audio</label>
      <input type="file" class="form-control" @change="uploadFile">
      <audio :src="attachment.url" controls/>
      <!--<div class="invalid-feedback d-block">
        The file must mp3!
      </div>-->
    </div>
  </div>
</template>

<script>
  export default {
    name: "AudioForm",
    props: {
      value: {
        type: String,
        default: undefined
      }
    },
    data: () => ({
      attachment: {
        url: null
      }
    }),
    methods: {
      uploadFile (e) {
        this.deleteFile();

        let formData = new FormData();
        formData.append('file', e.target.files[0]);

        this.request('api.back-office.attachment', {}, {
          headers: {
            'Content-Type': 'multipart/form-data'
          },
          data: formData
        }).then((response) => {
          this.attachment = response.data;
          this.$emit('input', response.data.id)
        })
      },
      deleteFile () {
        if (this.attachment.id) {
          this.request('api.back-office.attachment.destroy', {
            attachment_uuid: this.attachment.id
          })
        }
      }
    },
    mounted () {
      if (this.value) {
        this.request('api.client.attachment.show.data', { attachment_uuid: this.value }, {
          'Accept': 'application/json'
        }).then(res => {
          this.attachment = res.data
        })
      }
    }
  }
</script>

<style scoped>
  audio {
    width: 100%;
  }
</style>
