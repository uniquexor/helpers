<?php
    namespace unique\helpers;

    /**
     * Klase pateikianti metodus dirbti su datomis
     */
    class LithuanianDates {

        protected static $months = array(
            'ko' => array(
                'sausio',
                'vasario',
                'kovo',
                'balandžio',
                'gegužės',
                'birželio',
                'liepos',
                'rugpjūčio',
                'rugsėjo',
                'spalio',
                'lapkričio',
                'gruodžio',
            )
        );
        
        /**
         * Gražina intervala nuo vienos datos iki kitos, neįtraukiant nereikalingu metu/mėnesių, pvz.: ( "2010-03-30", "2010-04-01" ) - grazins: "2010-03-30 - 04-01"
         *
         * @param string $nuo - Pirmoji data formatu ("YYYY-MM-DD")
         * @param string $iki - Antroji data formatu ("YYYY-MM-DD")
         * @return string
         */
        public static function dateRange( $nuo, $iki ) {

            $nuo = date( 'Y-m-d', strtotime( $nuo ) );
            $iki = date( 'Y-m-d', strtotime( $iki ) );

            if ( $nuo === $iki ) {
                
                return $nuo;
            } elseif ( date( 'Y-m', strtotime( $nuo ) ) == date( 'Y-m', strtotime( $iki ) ) ) {
        
                return $nuo . ' - ' . date( 'd', strtotime( $iki ) );
            } elseif ( date( 'Y', strtotime( $nuo ) ) == date( 'Y', strtotime( $iki ) ) ) {
        
                return $nuo . ' - ' . date( 'm-d', strtotime( $iki ) );
            } else {
        
                return $nuo . ' - ' . $iki;
            }
        }

        /**
         * Gražina intervala nuo vieno laiko iki kito, pvz.: ( "18:30", "19:00" ) - grazins: "18:30 - 19:00"
         *
         * @param string $nuo - Pirmoji data formatu ("YYYY-MM-DD")
         * @param string|null $iki - Antroji data formatu ("YYYY-MM-DD")
         * @return string
         */
        public static function timeRange( string $nuo, string|null $iki, bool $with_seconds = false ) {

            $format = 'H:i' . ( $with_seconds ? ':s' : '' );

            $nuo_date = date( $format, strtotime( $nuo ) );
            $iki_date = date( $format, strtotime( $iki ) );

            if ( $nuo_date === $iki_date ) {

                return $nuo_date;
            } else {

                return $nuo_date . ' - ' . ( $iki ?: '...' );
            }
        }

        /**
         * Gražina intervala nuo vieno datos/laiko iki kito, pvz.: ( "2000-01-01 18:30", "2000-01-01 19:00" ) - grazins: "2000-01-01 18:30 - 19:00"
         *
         * @param string $nuo - Pirmoji data formatu ("YYYY-MM-DD")
         * @param string $iki - Antroji data formatu ("YYYY-MM-DD")
         * @param bool $with_seconds - Ar įtraukti sekundes
         * @return string
         */
        public static function dateTimeRange( string $nuo, string $iki, bool $with_seconds = false ) {

            $nuo_date = date( 'Y-m-d', strtotime( $nuo ) );
            $iki_date = date( 'Y-m-d', strtotime( $iki ) );

            $format = 'H:i' . ( $with_seconds ? ':s' : '' );
            $nuo_time = date( $format, strtotime( $nuo ) );
            $iki_time = date( $format, strtotime( $iki ) );

            if ( $nuo_date === $iki_date ) {

                return $nuo_date . ' ' . self::timeRange( $nuo_time, $iki_time, $with_seconds );
            } else {

                return $nuo_date . ' ' . $nuo_time . ' - ' . $iki_date . ' ' . $iki_time;
            }
        }

        /**
         * Pilnu tekstu išvedamas datų intervalas, pvz.: "2010 m. Sausio 1 d. - 21 d."
         *
         * @param string $nuo - Datų intervalo pradžia
         * @param string $iki - Datų intervalo pabaiga
         * @param string $separator - (Optional, default="-") Skirtukas tarp datų.
         * @param bool $full_dates
         * @return string
         */
        public static function fullDateRange( $nuo, $iki, $separator = '-', $full_dates = false ) {

            if ( date( 'Y-m-d', strtotime( $nuo ) ) == date( 'Y-m-d', strtotime( $iki ) ) ) {

                return LithuanianDates::fullDate( $nuo );
            } elseif ( ( date( 'Y-m', strtotime( $nuo ) ) == date( 'Y-m', strtotime( $iki ) ) ) && ( !$full_dates ) ) {

                return mb_substr( LithuanianDates::fullDate( $nuo ), 0, -3 ) . ' ' . $separator . ' ' . date( 'j', strtotime( $iki ) ) . ' d.';
            } elseif ( ( date( 'Y', strtotime( $nuo ) ) == date( 'Y', strtotime( $iki ) ) ) && ( !$full_dates ) ) {

                $m = date( 'n', strtotime( $iki ) ) - 1;
                $d = date( 'j', strtotime( $iki ) );

                return mb_substr( LithuanianDates::fullDate( $nuo ), 0, -3 ) . ' ' . $separator . ' ' .
                    LithuanianDates::$months['ko'][ $m ] . ' mėn. ' . $d . ' d.';
            } else {

                return LithuanianDates::fullDate( $nuo ) . ' ' . $separator . ' ' . LithuanianDates::fullDate( $iki );
            }
        }
        
        /**
         * Pilnu tekstu išvedama data, pvz.: "2010 m. Sausio 1 d."
         *
         * @param string $date
         * @return string
         */
        public static function fullDate( $date ) {

            $ret = '';
            
            if ( preg_match( '/([0-9]{4})-([0-9]{2})-([0-9]{2})/', $date, $regs ) ) {
            
                $ret = $regs[1] . ' m. ' . LithuanianDates::$months['ko'][ ( $regs[2] - 1 ) ] . ' ' . $regs[3] . ' d.';
            } elseif ( preg_match( '/([0-9]{2})-([0-9]{2})/', $date, $regs ) ) {
            
                $ret = LithuanianDates::$months['ko'][ ( $regs[1] - 1 ) ] . ' ' . $regs[2] . ' d.';
            } elseif ( preg_match( '/([0-9]{2})/', $date, $regs ) ) {
            
                $ret = $regs[1] . ' d.';
            }
            
            return $ret;
        }

        /**
         * Sugeneruoja datų pradžios ir pabaigos intervalą, kai paduodama pradžios data ir kiek dienų.
         * Pvz.: `2019-01-01 - 05` arba: `2019-01-28 - 02-02`
         *
         * @param string $date_start - Pradžios data. Formatas: YYYY-MM-DD
         * @param int $days - Už kiek dienų bus pabaiga.
         * @return string
         */
        public static function dateRangeByDays( $date_start, $days ) {

            if ( ( $days - 1 ) === 0 ) {

                return $date_start;
            }

            $date_end = date( 'Y-m-d', strtotime( $date_start . ' +' . ( $days - 1 ) . ' DAYS' ) );
            return self::dateRange( $date_start, $date_end );
        }
    }