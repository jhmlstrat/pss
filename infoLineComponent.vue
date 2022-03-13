var InfoLineComponent = {
  name: 'InfoLineComponent',
  template: `
    <div class="container">
      <b-row>
        <b-col cols="2">
        </b-col>
        <b-col cols="2">
          HOLD: {{ hold }}
        </b-col>
        <b-col cols="4">
        </b-col>
        <b-col cols="2">
          BPHR: {{ bphrl }} / {{ bphrr }}
        </b-col>
        <b-col cols="2">
          BPS: {{ bpsl }} / {{ bpsr }}
        </b-col>
      </b-row>
<!--
<hr>
{{ gameInfo }}
<hr>
{{ vRoster }}
<hr>
{{ hRoster }}
<hr>
-->
    </div>
  `,
  props: ['gameInfo','vRoster','hRoster'],
  data: function() {
    return {
      hold: 0,
      bphrl: '0',
      bphrr: '0',
      bpsl: '0',
      bpsr: '0',
    }
  },
  watch: {
    gameInfo() {
      this.update();
    },
  },
  mounted() {
    this.update();
  },
  methods: {
    update() {
      //console.log(vue.cyConfig);
      for (tm of vue.cyConfig.teams) {
        if (tm.team == this.gameInfo.home.name) {
          this.bphrl = (tm.weather.robbing.left ? '' : '*') + tm.weather.values[this.gameInfo.weather].bphr.left;
          this.bphrr = (tm.weather.robbing.right ? '' : '*') + tm.weather.values[this.gameInfo.weather].bphr.right;
          this.bpsl = tm.weather.values[this.gameInfo.weather].bps.left;
          this.bpsr = tm.weather.values[this.gameInfo.weather].bps.right;
        }
      }
      // TBD - HOLD
    },
  },
}


