require('./bootstrap');

import React from 'react';
import { render } from 'react-dom';

import Master from './components/Master';
import Footer from './components/Footer';
import HomeSearchForm from './components/HomeSearchForm';
import Subscribe from './components/Subscribe';
import RoomList from './components/Room/RoomList';
import Room from './components/Room/Room';
import Auth from './components/Auth';

render(<Master />, document.getElementById('Master'));