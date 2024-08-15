/**
 * Athlete Dashboard Admin JavaScript
 *
 * This file handles the initialization and rendering of charts
 * on the Athlete Dashboard admin page, as well as the Athlete Admin App.
 *
 * @package AthleteDashboard
 */

(function(jQuery) {
    'use strict';

    jQuery(document).ready(function() {
        initializeCharts();
        initializeAthleteAdminApp();
    });

    function initializeCharts() {
        initializeAthleteCheckInsChart();
        initializeAthleteRegistrationChart();
    }

    function initializeAthleteCheckInsChart() {
        var chartElement = document.getElementById('athlete-check-ins-chart');
        if (chartElement) {
            wp.apiFetch({ path: 'athlete-dashboard/v1/check-in-stats' }).then(function(data) {
                var context = chartElement.getContext('2d');
                var chart = new Chart(context, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [
                            {
                                label: 'First-time Check-ins',
                                data: data.firstTimeCheckIns,
                                borderColor: 'rgb(75, 192, 192)',
                                tension: 0.1
                            },
                            {
                                label: 'Total Check-ins',
                                data: data.totalCheckIns,
                                borderColor: 'rgb(255, 99, 132)',
                                tension: 0.1
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            }).catch(function(error) {
                console.error('Error fetching check-in stats:', error);
            });
        }
    }

    function initializeAthleteRegistrationChart() {
        var chartElement = document.getElementById('athlete-registration-chart');
        if (chartElement) {
            var context = chartElement.getContext('2d');
            var chart = new Chart(context, {
                type: 'line',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July'],
                    datasets: [{
                        label: 'Athlete Registrations',
                        data: [5, 10, 15, 20, 25, 30, 35],
                        borderColor: 'rgb(255, 215, 0)', // Gold color
                        tension: 0.1
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    }
                }
            });
        }
    }

    /**
     * Initialize the Athlete Admin App React component.
     */
    function initializeAthleteAdminApp() {
        const { useState, useEffect, createElement } = wp.element;
        const { Button, Card, CardBody, CardHeader, SelectControl } = wp.components;

        function AthleteAdminApp() {
            const [athletes, setAthletes] = useState([]);
            const [offers, setOffers] = useState([]);
            const [loading, setLoading] = useState(true);

            useEffect(() => {
                fetchAthletes();
                fetchOffers();
            }, []);

            const fetchAthletes = async () => {
                try {
                    const response = await wp.apiFetch({ path: 'athlete-dashboard/v1/athletes' });
                    setAthletes(response);
                    setLoading(false);
                } catch (error) {
                    console.error('Error fetching athletes:', error);
                    setLoading(false);
                }
            };

            const fetchOffers = async () => {
                try {
                    const response = await wp.apiFetch({ path: 'athlete-dashboard/v1/offers' });
                    setOffers(response);
                } catch (error) {
                    console.error('Error fetching offers:', error);
                }
            };

            const handleCheckIn = async (athleteId) => {
                try {
                    const response = await wp.apiFetch({
                        path: 'athlete-dashboard/v1/check-in',
                        method: 'POST',
                        data: { athlete_id: athleteId }
                    });
                    
                    if (response.success) {
                        console.log('Check-in successful. New count:', response.check_in_count);
                        // Update the athlete's check-in count in the state
                        setAthletes(athletes.map(athlete => 
                            athlete.id === athleteId 
                                ? {...athlete, check_in_count: response.check_in_count} 
                                : athlete
                        ));
                    } else {
                        console.error('Check-in failed:', response);
                        alert('Failed to check in. Please try again.');
                    }
                } catch (error) {
                    console.error('Error checking in:', error);
                    alert('An error occurred while checking in. Please try again.');
                }
            };

            const handleAssignOffer = async (athleteId, offerId) => {
                try {
                    const response = await wp.apiFetch({
                        path: 'athlete-dashboard/v1/assign-offer',
                        method: 'POST',
                        data: { athlete_id: athleteId, offer_id: offerId }
                    });
                    if (response.success) {
                        fetchAthletes(); // Refresh the list after assigning offer
                    } else {
                        console.error('Error assigning offer:', response);
                        // Display an error message to the user
                        alert('Failed to assign offer. Please try again.');
                    }
                } catch (error) {
                    console.error('Error assigning offer:', error);
                    // Display an error message to the user
                    alert('Failed to assign offer. Please try again.');
                }
            };

            if (loading) {
                return createElement('div', null, 'Loading...');
            }

            // Create the main component structure using React.createElement
            return createElement('div', null, 
                createElement('h2', null, 'Athlete Management'),
                athletes.map(athlete => 
                    createElement(Card, { key: athlete.id },
                        createElement(CardHeader, null, 
                            createElement('strong', null, athlete.name)
                        ),
                        createElement(CardBody, null, 
                            createElement('p', null, 'Email: ', athlete.email),
                            createElement('p', null, 'Active Offer: ', athlete.active_offer),
                            createElement('p', null, 'Check-in Count: ', athlete.check_in_count),
                            createElement(Button, { 
                                isPrimary: true, 
                                onClick: () => handleCheckIn(athlete.id) 
                            }, 'Check In'),
                            createElement(SelectControl, {
                                label: "Assign Offer",
                                value: athlete.active_offer,
                                options: [
                                    { label: 'Select an offer', value: '' },
                                    ...offers.map(offer => ({ label: offer.title, value: offer.id }))
                                ],
                                onChange: (offerId) => handleAssignOffer(athlete.id, offerId)
                            })
                        )
                    )
                )
            );
        }

        const athleteAdminAppContainer = document.getElementById('athlete-admin-app');
        if (athleteAdminAppContainer) {
            // Render the AthleteAdminApp component using React.createElement
            wp.element.render(createElement(AthleteAdminApp), athleteAdminAppContainer);
        }
    }

})(jQuery);