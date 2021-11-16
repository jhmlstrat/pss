var DefenseComponent = {
  name: 'DefenseComponent',
  template: `
    <div class="container">
      <b-row>
        <b-col cols="12" class="text-right">
          <b-button variant="link" @click="doLineup()">LINEUP</b-button>
        </b-col>
      </b-row>
      <b-row>
        <b-col cols=4 class="text-center">
        </b-col>
        <b-col cols=4 class="text-center">
          <!--
          <b-button variant="link" @click="replacePlayer(8)">
          -->
            CF - <span v-html="fielder(8)"></span>
          <!--
          </b-button> 
          -->
        </b-col>
        <b-col cols=4 class="text-center">
        </b-col>
      </b-row>
      <b-row>
        <b-col cols=4 class="text-center">
          <!--
          <b-button variant="link" @click="replacePlayer(7)">
          -->
            LF - <span v-html="fielder(7)"></span>
          <!--
          </b-button> 
          -->
        </b-col>
        <b-col cols=4 class="text-center">
        </b-col>
        <b-col cols=4 class="text-center">
          <!--
          <b-button variant="link" @click="replacePlayer(9)">
          -->
            RF - <span v-html="fielder(9)"></span>
          <!--
          </b-button> 
          -->
        </b-col>
      </b-row>
      <b-row>&nbsp;</b-row>
      <b-row>
        <b-col cols=2 class="text-center">
        </b-col>
        <b-col cols=3 class="text-center">
          <!--
          <b-button variant="link" @click="replacePlayer(6)">
          -->
            SS - <span v-html="fielder(6)"></span>
          <!--
          </b-button> 
          -->
        </b-col>
        <b-col cols=2 class="text-center">
        </b-col>
        <b-col cols=3 class="text-center">
          <!--
          <b-button variant="link" @click="replacePlayer(4)">
          -->
            2B - <span v-html="fielder(4)"></span>
          <!--
          </b-button> 
          -->
        </b-col>
        <b-col cols=2 class="text-center">
        </b-col>
      </b-row>
      <b-row>&nbsp;</b-row>
      <b-row>
        <b-col cols=3 class="text-center">
          <!--
          <b-button variant="link" @click="replacePlayer(5)">
          -->
            3B - <span v-html="fielder(5)"></span>
          <!--
          </b-button> 
          -->
        </b-col>
        <b-col cols=6 class="text-center">
          P - <span v-html="fielder(1)"></span>
        </b-col>
        <b-col cols=3 class="text-center">
          <!--
          <b-button variant="link" @click="replacePlayer(3)">
          -->
            1B - <span v-html="fielder(3)"></span>
          <!--
          </b-button> 
          -->
        </b-col>
      </b-row>
      <b-row>&nbsp;</b-row>
      <b-row>
        <b-col cols=4 class="text-center">
        </b-col>
        <b-col cols=4 class="text-center">
          <!--
          <b-button variant="link" @click="replacePlayer(2)">
          -->
            C - <span v-html="fielder(2)"></span>
          <!--
          </b-button> 
          -->
        </b-col>
        <b-col cols=4 class="text-center">
        </b-col>
      </b-row>
    </div>
  `,
  props: ['gameInfo','vRoster','hRoster'],
  data: function() {
    return {
    }
  },
  watch: {
  },
  mounted() {
  },
  computed: {
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    roster() {
      if (this.gameInfo.situation.situation.side == 0) {
        return this.hRoster;
      }
      return this.vRoster;
    },
    fielders(pos) {
      f = ['DH', 'P', 'C', 'B1', 'B2', 'B3', 'SS', 'LF', 'CF', 'RF', 'PH', 'PR'];
      return f[pos];
    },
    playerByPos(pos) {
      if (this.gameInfo.situation.situation.side == 0) {
        s = this.gameInfo.home;
      } else {
        s = this.gameInfo.visitor;
      }
      //console.log(s);
      slot = -1;
      player = null;
      if (pos != 1) {
        for (var sp in s.lineup) {
          pl = s.lineup[sp][s.lineup[sp].length - 1]
          cpos = pl.player.positions[pl.player.positions.length - 1].position.pos
          if (cpos != this.fielders(pos)) continue;
          slot = sp; 
        }
        if (slot == -1) return '';
        player = s.lineup[slot][s.lineup[slot].length - 1];
      } else {
        player = s.rotation[s.rotation.length -1];
      }
      play = null;
      //console.log(this.roster().roster);
      for (r in this.roster().roster.batters) {
        if (this.roster().roster.batters[r].rosterItem.player.name == player.player.name) {
          play = this.roster().roster.batters[r].rosterItem.player;
        }
      }
      if (play == null) {
        for (r in this.roster().roster.pitchers) {
          if (this.roster().roster.pitchers[r].rosterItem.player.name == player.player.name) {
            play = this.roster().roster.pitchers[r].rosterItem.player;
          }
        }
      }
      return {"play":play, "slot":slot, "due":(slot == s.results.length%9 ? true: false)};
    },
    fielder(pos) {
      r = this.playerByPos(pos);
      play = r.play;
      slot = r.slot;
      slot++;
      title = '5eMAX'
      if (play != null) {
        //for (var r in this.roster().roster) {
        //}
        for (p in play.strat.positionsPlayed) {
          if (play.strat.positionsPlayed[p].position.pos == this.fielders(pos)) {
            title = play.strat.positionsPlayed[p].position.rating + 'e' + play.strat.positionsPlayed[p].position.e;
            if (pos == 2 || pos == 7 || pos ==8 || pos == 9) {
              title += ' (' + play.strat.positionsPlayed[p].position.arm + ')';
            }
          }
        }
      } 
      rtn = '<span title="' + title + '"';
      rtn += '>' + player.player.name;
      if (pos != 1) {
        rtn += ' (' + slot + (r.due?'*':'') + ')';
      }
      rtn += '</span>';
      return rtn;
    },
    replacePlayer(pos) {
      r = this.playerByPos(pos);
      play = r.play;
      slot = r.slot;
      //console.log(play);
      if (this.gameInfo.situation.situation.side == 0) {
        s = this.gameInfo.home;
      } else {
        s = this.gameInfo.visitor;
      }
      //console.log(s);
      avail = [] 
    },
    doLineup() {
      vue.currentComponent = "lineupsComponent";
    },
  },
};

