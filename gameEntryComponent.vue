var GameEntryComponent = {
  name: 'GameEntryComponent',
  template: `
    <div class="container">
GameEntryComponent
      <b-row>
        <b-col cols="11">
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doPlay('undo')" >Undo</b-button> 
        </b-col>
      </b-row>
      <b-row v-show="! rarePlay">
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doPlay('WP')" v-show="(gameInfo.situation.situation.first != '' || gameInfo.situation.situation.second != '' || gameInfo.situation.situation.third != '') ||
                                                (plus && other == 'K' && gameInfo.situation.situation.first == '' && gameInfo.situation.situation.second == '' && gameInfo.situation.situation.third == '')">WP</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doPlay('B')" v-show="(gameInfo.situation.situation.first != '' || gameInfo.situation.situation.second != '' || gameInfo.situation.situation.third != '')">B</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doPlay('PB')" v-show="(gameInfo.situation.situation.first != '' || gameInfo.situation.situation.second != '' || gameInfo.situation.situation.third != '') ||
                                                (plus && other == 'K' && gameInfo.situation.situation.first == '' && gameInfo.situation.situation.second == '' && gameInfo.situation.situation.third == '')">PB</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doPlay('SB')" v-show="(gameInfo.situation.situation.first != '' || gameInfo.situation.situation.second != '' || gameInfo.situation.situation.third != '')">SB</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doPlay('CS')" v-show="(gameInfo.situation.situation.first != '' || gameInfo.situation.situation.second != '' || gameInfo.situation.situation.third != '')">CS</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doPlay('PO')" v-show="(gameInfo.situation.situation.first != '' || gameInfo.situation.situation.second != '' || gameInfo.situation.situation.third != '')">PO</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doPlay('DI')" v-show="(gameInfo.situation.situation.first != '' || gameInfo.situation.situation.second != '' || gameInfo.situation.situation.third != '')">DI</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doRarePlay()" v-show="! plus">RP</b-button>
          <b-button variant="link" href="#" v-on:click="doInjury()" v-show="plus">Injury</b-button>
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doPlus()" v-show="! plus">+</b-button>
          <b-button variant="link" href="#" v-on:click="doMinus()" v-show="plus">-</b-button>
        </b-col>
      </b-row>
      <b-row v-show="rarePlay">
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With runner on first base, line drive hits runner. Runner out, batter to first on a single, other runners hold" variant="link" href="#" v-on:click="doPlay('RP-S1')">Rare S1</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Batter singles, runners advancing two bases. Fielder throws behind batter who is out at first" variant="link" href="#" v-on:click="doPlay('RP-S2')">Rare S2</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Batter doubles, runners advancing two bases. Fielder throws behind batter who is out at second on the hidden ball play" variant="link" href="#" v-on:click="doPlay('RP-D2')">Rare D2</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With no runners on, the batter hits a double, but is out on an appeal of missing first base" variant="link" href="#" v-on:click="doPlay('RP-D3')">Rare D3</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Fielders collide, inside the park home run" variant="link" href="#" v-on:click="doPlay('RP-T3')">Rare T3</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With runner on first, runner breaks up the double play, but is called out interference. Runner and batter out - other runners do not advance.  No runner on first - batter out, runners all hold" variant="link" href="#" v-on:click="doPlay('RP-G1')">Rare G1</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Batter hits a shot off the pitcher to the fielder. Batter out at first, runners advance only if forced" variant="link" href="#" v-on:click="doPlay('RP-G2')">Rare G2</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Batter hits a shot off the mound. Batter out at first, runners advance 1 base (even with IF in)" variant="link" href="#" v-on:click="doPlay('RP-G3')">Rare G3</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button variant="link" href="#" v-on:click="doRarePlay()" v-show="! plus">RP</b-button>
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
      </b-row>
      <b-row v-show="rarePlay">
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Outfielder makes the catch, but crashes into the wall and is dazed. Runners tag and advance 2 bases" variant="link" href="#" v-on:click="doPlay('RP-F1')">Rare F1</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With runner on 3rd, outfielder makes catch and runner is out on an appeal for leaving early" variant="link" href="#" v-on:click="doPlay('RP-F2')">Rare F2</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Outfielder makes a running catch and lead runner is unable to get back - double play" variant="link" href="#" v-on:click="doPlay('RP-F3')">Rare F3</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With runners on - WP that catcher cannot find, runners advance 2 bases. No runners on - K + WP, batter to first" variant="link" href="#" v-on:click="doPlay('RP-W/S')">Rare W/S</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With runners on - WP, runners advance 1 base. No runners on - K + WP, batter to first" variant="link" href="#" v-on:click="doPlay('RP-W/G')">Rare W/G</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Catcher's interference, batter to first, runners advance if forced." variant="link" href="#" v-on:click="doPlay('RP-P/F')">Rare P/F</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="With runners on - PB with runner on 3rd holding, other runners advance if possible. No runners on - K + PB, batter to first" variant="link" href="#" v-on:click="doPlay('RP-P/P')">Rare P/P</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
          <b-button v-b-tooltip.hover title="Infield popup no one takes charge of, drops for a Single*" variant="link" href="#" v-on:click="doPlay('RP-PO')">Rare PO</b-button> 
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
        <b-col cols="1" class="text-right">
        </b-col>
      </b-row>
      <b-row v-show="!rarePlay">
        <b-col cols="12">
          <b-button variant="link" href="#" v-on:click="doPlay('gbA')">gbA</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('gbB')">gbB</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('gbC')">gbC</b-button> 
<!--
          <b-button variant="link" href="#" v-on:click="doPlay('gb+')">gb+</b-button> 
-->
          <b-button variant="link" href="#" v-on:click="doPlay('gb!')">gb!</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('SAC')">SAC</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('flyA')">flyA</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('flyB')">flyB</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('flyC')">flyC</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('fly!')">fly!</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('lo')">lo</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('lo!')">lo!</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('K')">K</b-button> 
  
        </b-col>
      </b-row>
      <b-row v-show="!rarePlay">
        <b-col cols="12">
          <b-button variant="link" href="#" v-on:click="doPlay('S*')">S*</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('S**')">S**</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('S-dot')">S-dot</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('S!')">S!</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('BPS+')">BPS+</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('BPS-')">BPS-</b-button>
          <b-button variant="link" href="#" v-on:click="doPlay('D**')">D**</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('D***')">D***</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('D!')">D!</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('T')">T</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('HR')">HR</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('BPHR+')">BPHR+</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('BPHR-')">BPHR-</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('BB')">BB</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('HBP')">HBP</b-button> 
          <b-button variant="link" href="#" v-on:click="doPlay('E')">E</b-button> 
<!--
          <b-button variant="link" href="#" v-on:click="doPlay('CI')">CI</b-button> 
-->
        </b-col>
      </b-row>
      <b-modal id="verify-runners" title="Verify Runners" hide-header-close>
      </b-modal>
<!--
    {{ gameInfo }}   
-->
    </div>
  `,
  props: ['gameInfo'],
  data: function() {
    return {
      team: {'team_name':''},
      teamname: 't',
      plus: false,
      rarePlay: false,
      other: '',
      huh: false,
    }
  },
  watch: {
  },
  mounted() {
    eBus.$on('teamUpdated',(t) => { this.team = t;});
  },
  methods: {
    currentComponent() { if (vue == undefined) return ''; return vue.currentComponent; },
    doInjury() {
    },
    doMinus() {
      this.plus = false;
    },
    doPlay(play) {
      console.log(play);
      this.huh = false;
      addBat=0;
      addFirst=0;
      addSecond=0;
      addThird=0;
      after='';
      injury = false;
      error = false;
      needModal = false;
      if (this.plus && this.other == "") {
        this.other = play;
        return;
      }
      if (this.plus) {
        if (this.play == 'E' || this.other == 'E') {
          if (this.play == 'E') this.play == this.other;
          error = true;
        } else if (this.play == 'Injury' || this.other == 'Injury') {
          if (this.play == 'Injury') this.play == this.other;
          injury = true;
        } else {
          this.huh = true;
        }
      }
      this.plus = false;
      if (this.huh) return;
      realPlay = play;
      sit = this.gameInfo.situation.situation;
      //console.log(sit);
      if (this.play == 'undo') {
        needModal = false;
      } else if (play == 'WP') {
        needModal = false;
        if (sit.first != '') addFirst ++;
        if (sit.second != '') addSecond ++;
        if (sit.third != '') addThird ++;
      } else if (play == 'B') {
        needModal = false;
        if (sit.first != '') addFirst ++;
        if (sit.second != '') addSecond ++;
        if (sit.third != '') addThird ++;
      } else if (play == 'PB') {
        needModal = false;
        if (sit.first != '') addFirst ++;
        if (sit.second != '') addSecond ++;
        if (sit.third != '') addThird ++;
      } else if (play == 'SB') {
        baseCount = 0;
        if (sit.first != '') { addFirst ++; baseCount++; }
        if (sit.second != '') { addSecond ++; baseCount++; }
        if (sit.third != '') { addThird ++; baseCount++; }
        if (baseCount == 1) needModal = false;
        else needModal = true;
      } else if (play == 'CS') {
        baseCount = 0;
        if (sit.first != '') { addFirst --; baseCount++; }
        if (sit.second != '') { addSecond --; baseCount++; }
        if (sit.third != '') { addThird --; baseCount++; }
        if (baseCount == 1) needModal = false;
        else needModal = true;
      } else if (play == 'PO') {
        baseCount = 0;
        if (sit.first != '') { addFirst --; baseCount++; }
        if (sit.second != '') { addSecond --; baseCount++; }
        if (sit.third != '') { addThird --; baseCount++; }
        if (baseCount == 1) needModal = false;
        else needModal = true;
      } else if (play == 'DI') {
        baseCount = 0;
        if (sit.first != '') { addFirst ++; baseCount++; }
        if (sit.second != '') { addSecond ++; baseCount++; }
        if (sit.third != '') { addThird ++; baseCount++; }
        if (baseCount == 1) needModal = false;
        else needModal = true;
      } else if (play == 'RP-S1') {
        realPlay="S";
        needModal = false;
        addBat++;
        if (sit.first != '') {
          addFirst = -1;
        } else {
          if (sit.second != '') addSecond ++;
          if (sit.third != '') addThird ++;
        }
      } else if (play == 'RP-S2') {
        realPlay="S";
        needModal = false;
        if (sit.first != '') addFirst = addFirst + 2;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird = addThird + 1;
        addBat = -1;
      } else if (play == 'RP-D2') {
        realPlay="D";
        needModal = false;
        if (sit.first != '') addFirst = addFirst + 2;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird = addThird + 1;
        addBat = -2;
      } else if (play == 'RP-D3') {
        needModal = false;
        baseCount = 0;
        if (sit.first != '') { addFirst = addFirst + 3; baseCount++; }
        if (sit.second != '') { addSecond = addSecond + 2; baseCount++; }
        if (sit.third != '') { addThird = addThird + 1; baseCount++; }
        if (basecount == 0) {
          realPlay="3";
          addBat = 2;
        } else {
          realPlay="D";
          addBat = 2;
        }
      } else if (play == 'RP-T3') {
        realPlay="HR";
        needModal = false;
        if (sit.first != '') addFirst = addFirst + 3;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird = addThird + 1;
        addBat = 4;
      } else if (play == 'RP-G1') {
        needModal = false;
        addBat = -1;
        if (sit.first != '') {
          realPlay = "6-4-3/DP";
          addFirst = -1;
        } else {
          realPlay = "6-3";
        }
      } else if (play == 'RP-G2') {
        needModal = false;
        realPlay = "6-3";
        addBat = -1;
        if (sit.first != '') {
          addFirst ++;
          if (sit.third != '') {
            addSecond ++;
            if (sit.third != '') addThird++;
          }
        }
      } else if (play == 'RP-G3') {
        needModal = false;
        realPlay = "6-3";
        addBat = -1;
        if (sit.first != '') addFirst ++;
        if (sit.second != '') addSecond ++;
        if (sit.third != '') addThird ++;
      } else if (play == 'RP-F1') {
        needModal = false;
        realPlay="8";
        addBat = -1;
        if (sit.first != '') addFirst = addFirst + 2;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird = addThird + 1;
      } else if (play == 'RP-F2') {
        needModal = false;
        addBat = -1;
        if (sit.third != '') {
          realPlay="8;8-5/DP";
          addThird = -1;
        } else {
          realPlay="8";
        }
      } else if (play == 'RP-F3') {
        needModal = false;
        addBat = -1;
        if (sit.third != '') {
          realPlay="8;8-5/DP";
          addThird = -1;
        } else if (sit.second != '') {
          realPlay="8;8-6/DP";
          addSecond = -1;
        } else if (sit.first != '') {
          realPlay="8;8-3/DP";
          addFirst = -1;
        } else {
          realPlay="8";
        }
      } else if (play == 'RP-W/S') {
        needModal = false;
        baseCount = 0;
        if (sit.first != '') { addFirst = addFirst + 2; baseCount++; }
        if (sit.second != '') { addSecond = addSecond + 2; baseCount++; }
        if (sit.third != '') { addThird = addThird + 1; baseCount++; }
        if (basecount == 0) {
          realPlay = "K;WP";
          addBat++;
        } else {
          realPlay = "WP-2";
        }
      } else if (play == 'RP-W/G') {
        needModal = false;
        baseCount = 0;
        if (sit.first != '') { addFirst = addFirst + 1; baseCount++; }
        if (sit.second != '') { addSecond = addSecond + 1; baseCount++; }
        if (sit.third != '') { addThird = addThird + 1; baseCount++; }
        if (basecount == 0) {
          realPlay = "K;WP";
          addBat++;
        } else {
          realPlay = "WP";
        }
      } else if (play == 'RP-P/F') {
        needModal = false;
        realPlay = "CI";
        addBat++;
        if (sit.first != '') {
          addFirst ++;
          if (sit.third != '') {
            addSecond ++;
            if (sit.third != '') addThird++;
          }
        }
      } else if (play == 'RP-P/P') {
        if (sit.first != '' || sit.second != '' || sit.third != '') {
          if (sit.third != '') {
            if (sit.second == '') {
              if (sit.first != '') {
                realPlay = "PB";
                addFirst++;
              } else realPlay = "";
            } else realPlay = "";
          } else {
            realPlay = "PB";
            if (sit.first != '') addFirst++;
            if (sit.second != '') addSecond ++;
          }
        } else {
          realPlay = "K;PB";
          addBat++;
        }
      } else if (play == 'RP-PO') {
        realPlay="S";
        needModal = false;
        addBat++;
        if (sit.first != '') addFirst++;
        if (sit.second != '') addSecond ++;
        if (sit.third != '') addThird ++;
      } else if (play == 'gbA') {
//TBD
      } else if (play == 'gbB') {
//TBD
      } else if (play == 'gbC') {
        needModal = true
        realPlay = "6-3";
        addBat = -1;
        if (sit.first != '') addFirst++;
        if (sit.second != '') addSecond ++;
        if (sit.third != '') addThird ++;
      } else if (play == 'gb+') {
        realPlay="S(plus)";
        needModal = false;
        addBat++;
        if (sit.first != '') addFirst = addFirst + 2;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird ++;
      } else if (play == 'gb!') {
        needModal = true
        realPlay = "6-3";
        addBat = -1;
      } else if (play == 'SAC') {
        needModal = true
        realPlay = "6-3(SAC)";
        addBat = -1;
        if (sit.first != '') addFirst++;
        if (sit.second != '') addSecond ++;
        if (sit.third != '') addThird ++;
      } else if (play == 'flyA') {
        needModal = true
        realPlay = "8";
        addBat = -1;
        if (sit.first != '') addFirst++;
        if (sit.second != '') addSecond ++;
        if (sit.third != '') addThird ++;
      } else if (play == 'flyB') {
        needModal = true
        realPlay = "8";
        addBat = -1;
        if (sit.third != '') addThird ++;
      } else if (play == 'flyC') {
        needModal = true
        realPlay = "8";
        addBat = -1;
      } else if (play == 'fly!') {
        needModal = true
        realPlay = "8";
        addBat = -1;
      } else if (play == 'lo') {
        realPlay="6";
        needmodal = true;
        addBat = -1;
      } else if (play == 'lo!') {
        realPlay="6";
        needmodal = true;
        addBat = -1;
      } else if (this.play == 'K') {
        needModal = false;
        addbat = -1;
      } else if (play == 'S*') {
        realPlay="S";
        needModal = false;
        addBat++;
        if (sit.first != '') addFirst++;
        if (sit.second != '') addSecond ++;
        if (sit.third != '') addThird ++;
      } else if (play == 'S**') {
        realPlay="S";
        needModal = false;
        addBat++;
        if (sit.first != '') addFirst = addFirst + 2;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird ++;
      } else if (play == 'S-dot') {
        realPlay="S(dot)";
        needModal = false;
        addBat++;
        if (sit.first != '') addFirst = addFirst + 2;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird ++;
      } else if (play == 'S!') {
      } else if (play == 'BPS+') {
        realPlay="S(bp)";
        needModal = false;
        addBat++;
        if (sit.first != '') addFirst++;
        if (sit.second != '') addSecond ++;
        if (sit.third != '') addThird ++;
      } else if (play == 'BPS-') {
        realPlay="6(bp)";
        needModal = false;
        addBat = -1;
      } else if (play == 'D**') {
        realPlay="D";
        needModal = false;
        addBat++;
        addBat++;
        if (sit.first != '') addFirst = addFirst + 2;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird ++;
      } else if (play == 'D***') {
        realPlay="D";
        needModal = false;
        addBat++;
        addBat++;
        if (sit.first != '') addFirst = addFirst + 3;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird ++;
      } else if (play == 'D!') {
        realPlay="D";
        needModal = true;
        addBat++;
        addBat++;
        if (sit.first != '') addFirst = addFirst + 2;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird ++;
      } else if (play == 'T') {
        needModal = false;
        addBat++;
        addBat++;
        addBat++;
        if (sit.first != '') addFirst = addFirst + 3;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird ++;
      } else if (play == 'HR') {
        realPlay="HR";
        needModal = false;
        addBat = 4;
        if (sit.first != '') addFirst = addFirst + 3;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird ++;
      } else if (play == 'BPHR+') {
        realPlay="HR(bp)";
        needModal = false;
        addBat = 4;
        if (sit.first != '') addFirst = addFirst + 3;
        if (sit.second != '') addSecond = addSecond + 2;
        if (sit.third != '') addThird ++;
      } else if (play == 'BPHR-') {
        realPlay="7(bp)";
        needModal = true;
        addBat = -1;
        if (sit.third != '') { addThird ++; realPlay="7(bp)/SF"; }
      } else if (play == 'BB') {
        needModal = false;
        addBat++;
        if (sit.first != '') {
          addFirst ++;
          if (sit.third != '') {
            addSecond ++;
            if (sit.third != '') addThird++;
          }
        }
      } else if (play == 'HBP') {
        needModal = false;
        addBat++;
        if (sit.first != '') {
          addFirst ++;
          if (sit.third != '') {
            addSecond ++;
            if (sit.third != '') addThird++;
          }
        }
      } else if (play == 'E') {
        error = true;
      //} else if (play == 'CI') {
      } else {
        console.log('Should not get here');
        realPlay = "";
      }
      this.rarePlay = false;
      this.plus = false;
      if (needModal || error) {
      } else if (realPlay != '') {
      }
    },
    doPlus() {
      this.plus = true;
      this.otherPlay = "";
    },
    doRarePlay() {
      this.rarePlay = ! this.rarePlay;
    },
    unusualModal() {
    },
    sendPlay() {
    },
  },
};
