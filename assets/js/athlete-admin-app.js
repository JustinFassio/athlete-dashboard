const { useState, useEffect } = wp.element;
const { Button, Card, CardBody, CardHeader, SelectControl } = wp.components;
const { createElement: h } = wp.element;

function AthleteAdminApp() {
    const [athletes, setAthletes] = useState([]);
    const [offers, setOffers] = useState([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        fetchAthletes();
        fetchOffers();
    }, []);

    // Fetch athletes data from the API
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

    // Fetch available offers from the API
    const fetchOffers = async () => {
        try {
            const response = await wp.apiFetch({ path: 'athlete-dashboard/v1/offers' });
            setOffers(response);
        } catch (error) {
            console.error('Error fetching offers:', error);
        }
    };

    // Handle check-in action for an athlete
    const handleCheckIn = async (athleteId) => {
        try {
            await wp.apiFetch({
                path: 'athlete-dashboard/v1/check-in',
                method: 'POST',
                data: { athlete_id: athleteId }
            });
            fetchAthletes(); // Refresh the list after check-in
        } catch (error) {
            console.error('Error checking in:', error);
        }
    };

    // Handle assigning an offer to an athlete
    const handleAssignOffer = async (athleteId, offerId) => {
        try {
            await wp.apiFetch({
                path: 'athlete-dashboard/v1/assign-offer',
                method: 'POST',
                data: { athlete_id: athleteId, offer_id: offerId }
            });
            fetchAthletes(); // Refresh the list after assigning offer
        } catch (error) {
            console.error('Error assigning offer:', error);
        }
    };

    if (loading) {
        return h('div', null, 'Loading...');
    }

    return h('div', null, 
        h('h2', null, 'Athlete Management'),
        athletes.map(athlete => 
            h(Card, { key: athlete.id },
                h(CardHeader, null, h('strong', null, athlete.name)),
                h(CardBody, null, 
                    h('p', null, 'Email: ', athlete.email),
                    h('p', null, 'Active Offer: ', athlete.active_offer),
                    h('p', null, 'Check-in Count: ', athlete.check_in_count),
                    h(Button, { isPrimary: true, onClick: () => handleCheckIn(athlete.id) }, 'Check In'),
                    h(SelectControl, {
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

// Render the AthleteAdminApp component in the 'athlete-admin-app' DOM element
wp.element.render(h(AthleteAdminApp), document.getElementById('athlete-admin-app'));