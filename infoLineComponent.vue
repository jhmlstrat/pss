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
          On Deck: <span v-html v-bind:title="onDeckTooltip()">{{onDeck()}}</span>
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
      hold: '-2 / 0',
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
    findCatcher(lineup) {
      // console.log(lineup);
      for (pos of lineup) {
        player = pos[pos.length-1].player;
        position = player.positions[player.positions.length-1].position.pos;
        // console.log(position);
        if (position == 'C') return player.name;
      }
      return null;
    },
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
      if (this.gameInfo.situation.situation.side == 0) {
        cn = this.findCatcher(this.gameInfo.home.lineup);
      } else {
        cn = this.findCatcher(this.gameInfo.visitor.lineup);
      }
      catcher = vue.getPlayerInfo(cn);
      // console.log(catcher);
      arm = '5';
      for (pos of catcher.strat.positionsPlayed) {
        if (pos.position.pos == 'C') arm = pos.position.arm;
      }
      pitcher = vue.getPlayerInfo(this.gameInfo.situation.situation.pitcher);
      // console.log(pitcher);
      this.hold = (parseInt(pitcher.strat.hold) + parseInt(arm) - 2) + ' / ' + (parseInt(pitcher.strat.hold) + parseInt(arm));
      // TBD - HOLD
    },
    onDeck() {
      if (this.gameInfo.situation.situation.side == 0) {
        lineup = this.gameInfo.visitor.lineup;
      } else {
        lineup = this.gameInfo.home.lineup;
      }
      nextI = -1;
      for ([index, lup] of lineup.entries()) {
        cb = lup[lup.length-1].player;
        if (cb.name == this.gameInfo.situation.situation.batter) {
          nextI = (index + 1)%9;
        }
      }
      return lineup[nextI][lineup[nextI].length-1].player.name;
    },
    onDeckTooltip() {
      return vue.batterTooltip(this.onDeck());
    },
  },
}


